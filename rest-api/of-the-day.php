<?php
/*
Description: REST-API: X Of The Day

The code that runs the X Of the Day API service
Every 24 hours, a new character and show of the day are spawned

Version: 1.0.0
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_OTD_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_OTD_JSON {

	/**
	 * Constructor
	 */
	function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init') );
	}

	/**
	 * Rest API init
	 *
	 * Creates callbacks
	 *   - /lwtv/v1/of-the-day/
	 */
	static function rest_api_init() {

		register_rest_route( 'lwtv/v1', '/of-the-day/', array(
			'methods' => 'GET',
			'callback' => array( $this, 'otd_rest_api_callback' ),
		) );

		register_rest_route( 'lwtv/v1', '/of-the-day/(?P<type>[a-zA-Z0-9-]+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'otd_rest_api_callback' ),
		) );

	}

	/**
	 * Rest API Callback for Of The Day
	 */
	static function otd_rest_api_callback( $data ) {
		$params = $data->get_params();
		$type   = ( isset( $params['type'] ) && $params['type'] !== '' )? sanitize_title_for_query( $params['type'] ) : 'unknown';
		$response = $this->of_the_day( $type );
		return $response;
	}
	
	/*
	 * Of the Day function
	 */
	static function of_the_day( $type = 'character' ) {

		// Valid types of 'of the day':
		$valid_types = array( 'birthday', 'character', 'show', 'death' );

		// If there's no known type, we'll assume character
		$type = ( !in_array( $type, $valid_types ) )? 'character' : $type;

		// Create the array
		if ( $type == 'death' ) {
			$of_the_day_array = LWTV_BYQ_JSON::on_this_day( date('m-d'), 'tweet' );
		} elseif ( $type == 'birthday' ) {
			$of_the_day_array = self::birthday( date('m-d') );
		} elseif ( $type == 'character' || $type == 'show' ) {
			$of_the_day_array = self::character_show( date('m-d'), $type );
		} else {
			$of_the_day_array = '';
		}

		if ( empty( $of_the_day_array) ) {
			return new WP_Error( 'no_type', 'Invalid content type given.', array( 'status' => 400 ) );
		}

		// No errors! Return array
		return $of_the_day_array;
	}


	/**
	 * character_show function.
	 * 
	 * @access public
	 * @param string $date (default: '')
	 * @param string $type (default: 'character')
	 * @return array
	 */
	static function character_show( $date = '', $type = 'character' ) {

		// Defaults...
		$return = array();
		$date   = ( $date == '' )? date('m-d') : $date;
		$type   = ( in_array( $type, array( 'show', 'character' ) ) )? $type : 'character';

		// Grab the options
		$default = array (
			'character' => array( 
				'time'  => strtotime( 'midnight tomorrow' ),
				'post'  => 'none',
			),
			'show'      =>  array( 
				'time'  => strtotime( 'midnight tomorrow' ),
				'post'  => 'none',
			),
		);
		$options = get_option( 'lwtv_otd', $default );

		// If there's no ID or the timestamp has past, we need a new ID
		if ( $options[ $type ][ 'post' ] == 'none' || time() >= $options[ $type ][ 'time' ] ) {
			add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

			$meta_query_array = '';
			$tax_query_array  = '';
			$char_tax_array   = self::character_awareness( date('m-d') );

			switch( $type ) {
				case 'character':
					$meta_query_array = array(
						array(
							'key'     => '_thumbnail_id',
							'value'   => '949', // Mystery woman
							'compare' => '!=',
						),
						array(
							'key'     => 'lezchars_show_group',
							'value'   => 're',
							'compare' => 'LIKE',
						)
					);
					$tax_query_array = $char_tax_array;
					break;
			}

			// Grab a random post
			$args = array( 
				'post_type'      => 'post_type_' . $type . 's',
				'orderby'        => 'rand', 
				'posts_per_page' => '1',
				's'              => '-TBD',
				'tax_query'      => $tax_query_array,
				'meta_query'     => $meta_query_array,
			);
			$post = new WP_Query( $args );

			// Do the needful
			while ( $post->have_posts() ) {
				$post->the_post();
				$id = get_the_ID();
			}
			wp_reset_postdata();
			
			// Update the options
			$options[ $type ][ 'post' ] = $id;
			$options[ $type ][ 'time' ] = strtotime( 'midnight tomorrow' );
			update_option( 'lwtv_otd', $options );
		}

		$post_id = $options[ $type ][ 'post' ];
		$image   = ( has_post_thumbnail( $post_id ) )? get_the_post_thumbnail_url( $post_id, 'full' ) : get_site_icon_url();

		// Base Array:
		$return = array(
			'id'     => $post_id,
			'name'   => get_the_title( $post_id ),
			'url'    => get_the_permalink( $post_id ),
			'image'  => $image,
		);

		// Add custom array items based on type
		switch( $type ) {
			case 'character':
				$all_shows        = lwtv_yikes_chardata( $post_id, 'shows' );
				if ( $all_shows !== '' ) {
					$show_title = array();
					foreach ( $all_shows as $each_show ) {
						array_push( $show_title, get_the_title( $each_show['show'] ) );
					}
				}
				$return['status'] = ( has_term( 'dead', 'lez_cliches' , $post_id ) )? 'dead' : 'alive';
				$return['shows']  = ( empty( $show_title ) )? ' None' : implode( ', ', $show_title );
				break;
			case 'show':
				$return['loved'] = ( get_post_meta( $post_id, 'lezshows_worthit_show_we_love', true ) )? 'yes' : 'no';
				break;
		}
		
		return $return;

	}

	/**
	 * Character Awareness Days
	 * 
	 * On visibility/awareness days, only show characters that are those things.
	 * 
	 * @param mixed $date
	 * @return array()
	 */
	static function character_awareness( $date = '' ) {

		$return = '';
		$date   = ( $date == '' )? date('m-d') : $date;

		switch( $date ) {
			case '03-31': // Transgender Day of Visibility
			case '11-20': // Transgender Day of Rememberance
				$return = array( 
					array( 'taxonomy' => 'lez_gender',    'field' => 'slug', 'terms' => array( 'trans-man', 'trans-woman' ) ) 
				);
				break;
			case '04-26': // Lesbian Visibility Day
				$return = array( 
					array( 'taxonomy' => 'lez_sexuality', 'field' => 'slug', 'terms' => array( 'homosexual' ) ),
					array( 'taxonomy' => 'lez_gender',    'field' => 'slug', 'terms' => array( 'cisgender', 'trans-woman' ) )
				);
			case '09-23': // Celebrate Bisexuality Day
				$return = array( 
					array( 'taxonomy' => 'lez_sexuality', 'field' => 'slug', 'terms' => array( 'bisexual' ) )
				);
				break;
			case '10-26': // Intersex Awareness Day 
			case '11-08': // Intersex Day of Remembrance
				$return = array( 
					array( 'taxonomy' => 'lez_gender',    'field' => 'slug', 'terms' => array( 'intersex' ) ) 
				);
				break;
		}

		return $return;
	}

	static function birthday( $date = '' ){

		$date   = ( $date == '' )? date('m-d') : $date;

		// Get all our birthdays
		$actor_loop  = LWTV_Loops::post_meta_query( 'post_type_actors', 'lezactors_birth', $date, 'LIKE' );

		if ( $actor_loop->have_posts() ) {
			foreach( $actor_loop->posts as $actor ) {

				// Get the post slug
				$post_slug = get_post_field( 'post_name', get_post( $actor ) );

				// Calculate Age
				$age_end = new DateTime( );
				if ( get_post_meta( $actor->ID, 'lezactors_death', true ) ) {
					$age_end = new DateTime( get_post_meta( $actor->ID, 'lezactors_death', true ) );
				}
				if ( get_post_meta( $actor->ID, 'lezactors_birth', true ) ) {
					$age_start = new DateTime( get_post_meta( $actor->ID, 'lezactors_birth', true ) );
				}
				if ( isset( $age_start ) ) {
					$alive = $age_start->diff( $age_end );
				}

				$age = $alive->format( '%Y');

				// Number Ordination
				$number_ends = array('th','st','nd','rd','th','th','th','th','th','th');
				if ( ($age %100 ) >= 11 && ( $age%100 ) <= 13) {
					$age .= 'th';
				} else {
					$age .= $number_ends[$age % 10];
				}

				// If they have a Twitter handle, use that
				// Else we use their name
				$name = ( get_post_meta( $actor->ID, 'lezactors_twitter', true ) )? '@' . get_post_meta( $actor->ID, 'lezactors_twitter', true ) :  get_the_title( $actor );

				// Add to array:
				$birthday_array[$post_slug] = $name . ' (' . $age . ')';
			}
		} else {
			// If no one has a birthday, whomp whomp
			return;
		}

		$return = array( 'date' => $date, 'birthdays' => implode( ', ', $birthday_array ) );

		return $return;

	}


}
new LWTV_OTD_JSON();