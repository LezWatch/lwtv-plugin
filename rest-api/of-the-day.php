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
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init') );
	}

	/**
	 * Rest API init
	 *
	 * Creates callbacks
	 *   - /lwtv/v1/of-the-day/
	 */
	public function rest_api_init() {

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
	public function otd_rest_api_callback( $data ) {
		$params = $data->get_params();
		$type   = ( isset( $params['type'] ) && $params['type'] !== '' )? sanitize_title_for_query( $params['type'] ) : 'unknown';
		$response = $this->of_the_day( $type );
		return $response;
	}
	
	/*
	 * Of the Day function
	 */
	public static function of_the_day( $type = 'unknown' ) {

		// Valid types of 'of the day':
		$valid_types = array( 'character', 'show', 'death' );

		// If there's no known type, we'll assume character
		if ( $type == 'unknown' || !in_array( $type, $valid_types ) ) {
			$type = 'character';
		}

		$of_the_day_array = array();

		// Death of the day is calculated differently
		if ( $type == 'death' ) {
			$of_the_day_array = LWTV_BYQ_JSON::on_this_day( date('m-d'), 'tweet' );
		} else {
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
				// Grab a random post
				$args = array( 
					'post_type'      => 'post_type_' . $type . 's',
					'orderby'        => 'rand', 
					'posts_per_page' =>'1',
					'meta_query' => array( 
						array(
							'key'     => '_thumbnail_id',
							'value'   => '949', // Mystery woman
							'compare' => '!=',
						),
						array(
							'key'     => 'lezchars_show_group',
							'value'   => array ( 'regular', 'recurring' ),
							'compare' => 'LIKE',
						),
					)
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
			$image   = get_site_icon_url();
			if ( has_post_thumbnail( $post_id ) ) {
				$image = get_the_post_thumbnail_url( $post_id, 'full' ) ;
			}
	
			// Base Array:
			$of_the_day_array = array(
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
					$of_the_day_array['status'] = ( has_term( 'dead', 'lez_cliches' , $post_id ) )? 'dead' : 'alive';
					$of_the_day_array['shows']  = ( empty( $show_title ) )? ' None' : implode( ', ', $show_title );
					break;
				case 'show':
					$of_the_day_array['loved'] = ( get_post_meta( $post_id, 'lezshows_worthit_show_we_love', true ) )? 'yes' : 'no';
					break;
			}
		}

		// Return array
		return $of_the_day_array;
	}

}
new LWTV_OTD_JSON();