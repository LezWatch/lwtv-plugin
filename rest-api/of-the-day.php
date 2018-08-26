<?php
/*
Description: REST-API: X Of The Day

The code that runs the X Of the Day API service
Every 24 hours, a new character and show of the day are spawned

Version: 1.0.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

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
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Rest API init
	 *
	 * Creates callbacks
	 *   - /lwtv/v1/of-the-day/
	 */
	public static function rest_api_init() {

		register_rest_route( 'lwtv/v1', '/of-the-day/', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'otd_rest_api_callback' ),
		) );
		register_rest_route( 'lwtv/v1', '/of-the-day/(?P<type>[a-zA-Z]+)', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'otd_rest_api_callback' ),
		) );
		register_rest_route( 'lwtv/v1', '/of-the-day/(?P<type>[a-zA-Z]+)/(?P<format>[a-zA-Z0-9-]+)', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'otd_rest_api_callback' ),
		) );
	}

	/**
	 * Rest API Callback for Of The Day
	 */
	public static function otd_rest_api_callback( $data ) {
		$params = $data->get_params();
		$type   = ( isset( $params['type'] ) && '' !== $params['type'] ) ? sanitize_title_for_query( $params['type'] ) : 'unknown';
		$format = ( isset( $params['format'] ) && '' !== $params['format'] ) ? sanitize_title_for_query( $params['format'] ) : 'default';

		$response = $this->of_the_day( $type, $format );
		return $response;
	}

	/*
	 * Of the Day function
	 */
	public static function of_the_day( $type = 'character', $format = 'default' ) {

		// Valid types of 'of the day'.
		// If there's no known type, we'll assume character
		$valid_types = array( 'birthday', 'character', 'show', 'death' );
		$type        = ( ! in_array( $type, $valid_types, true ) ) ? 'character' : $type;

		// Valid types of 'format'
		// If there's no known format, we'll assume character
		$valid_format = array( 'default', 'tweet', 'json' );
		$format       = ( ! in_array( $format, $valid_format, true ) ) ? 'default' : $format;

		// Create the date with regards to timezones
		$tz        = 'America/New_York';
		$timestamp = time();
		$dt        = new DateTime( 'now', new DateTimeZone( $tz ) ); //first argument "must" be a string
		$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp
		$date = $dt->format( 'm-d' );

		// Create the array
		switch ( $type ) {
			case 'death':
				$of_the_day_array = LWTV_BYQ_JSON::on_this_day( $date, $format );
				break;
			case 'birthday':
				$of_the_day_array = self::birthday( $date, $format );
				break;
			case 'character':
			case 'show':
				$of_the_day_array = self::character_show( $date, $type );
				break;
			default:
				$of_the_day_array = '';
				break;
		}

		if ( empty( $of_the_day_array ) ) {
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
	public static function character_show( $date = '', $type = 'character' ) {

		// Defaults...
		$return = array();

		// Grab the options
		$default = array(
			'character' => array(
				'time' => strtotime( 'midnight tomorrow' ),
				'post' => 'none',
			),
			'show'      => array(
				'time' => strtotime( 'midnight tomorrow' ),
				'post' => 'none',
			),
		);
		$options = get_option( 'lwtv_otd', $default );

		// If there's no ID or the timestamp has past, we need a new ID
		// Or if we're in dev mode.
		if ( 'none' === $options[ $type ]['post'] || time() >= $options[ $type ]['time'] || ( defined( 'LWTV_DEV_SITE' ) && LWTV_DEV_SITE ) ) {
			// Get the show ID
			$id = self::find_char_show( $type );

			// Update the options
			$options[ $type ]['post'] = $id;
			$options[ $type ]['time'] = strtotime( 'midnight tomorrow' );
			update_option( 'lwtv_otd', $options );

			// Set post_meta for the next available use (+4 months from now)
			update_post_meta( $id, 'lwtv_of_the_day', strtotime( '+4 months' ) );
		}

		$post_id = $options[ $type ]['post'];
		$image   = ( has_post_thumbnail( $post_id ) ) ? get_the_post_thumbnail_url( $post_id, 'full' ) : get_site_icon_url();

		// Base Array:
		$return = array(
			'id'    => $post_id,
			'name'  => get_the_title( $post_id ),
			'url'   => get_the_permalink( $post_id ),
			'image' => $image,
		);

		// Add custom array items based on type
		switch ( $type ) {
			case 'character':
				$all_shows   = get_post_meta( $post_id, 'lezchars_show_group', true );
				$shows_value = isset( $all_shows[0] ) ? $all_shows[0] : '';

				// Set Hashtag
				if ( ! empty( $shows_value ) ) {
					$num_shows = count( $all_shows );
					$showsmore = ( $num_shows > 1 ) ? ' (plus ' . ( $num_shows - 1 ) . ' more)' : '';
					$show_post = get_post( $shows_value['show'] );
					$hashtag   = '#' . preg_replace( '/[^A-Za-z0-9]/', '', $show_post->post_title );
				}
				// Set all shows (not used becuase of Sara Lance)
				if ( '' !== $all_shows && ! empty( $shows_value ) ) {
					$show_titles = array();
					foreach ( $all_shows as $each_show ) {
						array_push( $show_titles, get_the_title( $each_show['show'] ) );
					}
				}

				$return['status']  = ( has_term( 'dead', 'lez_cliches', $post_id ) ) ? 'dead' : 'alive';
				$return['shows']   = ( empty( $show_titles ) ) ? 'n/a' : implode( ', ', $show_titles );
				$return['hashtag'] = $hashtag;
				break;
			case 'show':
				$return['loved']   = ( get_post_meta( $post_id, 'lezshows_worthit_show_we_love', true ) ) ? 'yes' : 'no';
				$return['score']   = get_post_meta( $post_id, 'lezshows_the_score', true );
				$return['hashtag'] = '#' . preg_replace( '/[^A-Za-z0-9]/', '', get_the_title( $post_id ) );
				break;
		}

		return $return;

	}

	/**
	 * Let's find something valid...
	 * @param  string $type [character|show]
	 * @return number $id   [ID of the show or character]
	 */
	public static function find_char_show( $type = 'character' ) {

		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
			return false;
		}, 10, 2 );

		$meta_query_array = '';
		$tax_query_array  = '';

		switch ( $type ) {
			case 'character':
				$meta_query_array = array(
					array(
						'key'     => '_thumbnail_id',
						'value'   => '949', // Mystery woman
						'compare' => '!=',
					),
					array(
						'key'     => 'lezchars_show_group',
						'value'   => 're', // REgulars or REcurring, but not guest.
						'compare' => 'LIKE',
					),
				);
				$tax_query_array  = self::character_awareness( $date );
				break;
			case 'show':
				$meta_query_array = array(
					array(
						'key'     => 'lezshows_the_score',
						'value'   => '50', // Shows with a score over 50.
						'compare' => '>=',
					),
					array(
						'key'     => 'lezshows_worthit_rating',
						'value'   => 'e', // yEs or mEh, but not NO.
						'compare' => 'LIKE',
					),
				);
				break;
		}

		// Grab a random post
		$valid_post = false;

		while ( ! $valid_post ) {
			$args = array(
				'post_type'      => 'post_type_' . $type . 's',
				'orderby'        => 'rand',
				'posts_per_page' => '1',
				's'              => '-TBD', // Excluding posts with "TBD" as the content
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

			switch ( $type ) {
				case 'character':
					// if the character is a cartoon, they MUST be a regular.
					$is_toon = ( has_term( 'cartoon', 'lez_cliches', $id ) ) ? true : false;
					$is_regu = ( in_array( 'regular', get_post_meta( $id, 'lezchars_show_group', true ) ) ) ? true : false;
					if ( ! $is_toon || ( $is_toon && $is_regu ) ) {
						$valid_post = true;
					}
					break;
				case 'show':
					// All shows have to have at least one regular character
					$role_data = get_post_meta( $id, 'lezshows_char_roles', true );
					if ( 0 !== $role_data['regular'] ) {
						$valid_post = true;
					}
					break;
				default:
					$valid_post = true;
					break;
			}

			// If the time (now) is less than or equal to the last used AND it's
			// not empty, then it's not a valid post.
			// If it's not set at all, then we've never used it.
			$last_used = get_post_meta( $id, 'lwtv_of_the_day', true );
			if ( isset( $last_used ) && time() <= $last_used ) {
				$valid_post = false;
			}
		}

		return $id;
	}

	/**
	 * Character Awareness Days
	 *
	 * On visibility/awareness days, only show characters that are those things.
	 *
	 * @param mixed $date
	 * @return array()
	 */
	public static function character_awareness( $date = '' ) {

		$return = '';

		// Create the date with regards to timezones
		$tz        = 'America/New_York';
		$timestamp = time();
		$dt        = new DateTime( 'now', new DateTimeZone( $tz ) ); //first argument "must" be a string
		$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp
		$today = $dt->format( 'm-d' );
		$date  = ( '' === $date ) ? $date : $today;

		switch ( $date ) {
			case '03-31': // Transgender Day of Visibility
			case '11-20': // Transgender Day of Rememberance
				$return = array(
					array(
						'taxonomy' => 'lez_gender',
						'field'    => 'slug',
						'terms'    => array( 'trans-man', 'trans-woman' ),
					),
				);
				break;
			case '04-26': // Lesbian Visibility Day
				$return = array(
					array(
						'taxonomy' => 'lez_sexuality',
						'field'    => 'slug',
						'terms'    => array( 'homosexual' ),
					),
					array(
						'taxonomy' => 'lez_gender',
						'field'    => 'slug',
						'terms'    => array( 'cisgender', 'trans-woman' ),
					),
				);
				break;
			case '05-24': // Pansexual Day of Visibility
			case '12-08': // Pansexual Pride Day
				$return = array(
					array(
						'taxonomy' => 'lez_sexuality',
						'field'    => 'slug',
						'terms'    => array( 'pansexual' ),
					),
				);
				break;
			case '07-14': // Non-Binary Day
				$return = array(
					array(
						'taxonomy' => 'lez_gender',
						'field'    => 'slug',
						'terms'    => array( 'non-binary' ),
					),
				);
				break;
			case '09-23': // Celebrate Bisexuality Day
				$return = array(
					array(
						'taxonomy' => 'lez_sexuality',
						'field'    => 'slug',
						'terms'    => array( 'bisexual' ),
					),
				);
				break;
			case '10-26': // Intersex Awareness Day
			case '11-08': // Intersex Day of Remembrance
				$return = array(
					array(
						'taxonomy' => 'lez_gender',
						'field'    => 'slug',
						'terms'    => array( 'intersex' ),
					),
				);
				break;
		}

		return $return;
	}

	/**
	 * You say it's your birthday!
	 *
	 * @param  string $date   [description]
	 * @param  string $format [description]
	 * @return [type]         [description]
	 */
	public static function birthday( $date = '', $format = 'default' ) {

		// Get all our birthdays
		$actor_loop = LWTV_Loops::post_meta_query( 'post_type_actors', 'lezactors_birth', $date, 'LIKE' );

		if ( $actor_loop->have_posts() ) {
			foreach ( $actor_loop->posts as $actor ) {

				// Get the post slug
				$post_slug = get_post_field( 'post_name', get_post( $actor ) );

				// Calculate Age
				$age_end = new DateTime();
				if ( get_post_meta( $actor->ID, 'lezactors_death', true ) ) {
					$age_end = new DateTime( get_post_meta( $actor->ID, 'lezactors_death', true ) );
				}
				if ( get_post_meta( $actor->ID, 'lezactors_birth', true ) ) {
					$age_start = new DateTime( get_post_meta( $actor->ID, 'lezactors_birth', true ) );
				}
				if ( isset( $age_start ) ) {
					$alive = $age_start->diff( $age_end );
				}

				// Their age is ...
				$age = $alive->format( '%Y' );

				// Setup the WordPress name (used by LWTV News)
				$wordpress_name = '<a href="' . get_permalink( $actor ) . '">' . get_the_title( $actor ) . ' (' . $age . ')</a>';

				// If they have a Twitter handle, use that ; Else use their name
				$twitter_name = ( get_post_meta( $actor->ID, 'lezactors_twitter', true ) ) ? '@' . get_post_meta( $actor->ID, 'lezactors_twitter', true ) : get_the_title( $actor );

				// Add to array:
				$twitter_array[ $post_slug ]   = $twitter_name . ' (' . $age . ')';
				$wordpress_array[ $post_slug ] = $wordpress_name;

			}

			switch ( $format ) {
				case 'tweet':
					$birthdays = implode( ', ', $twitter_array );
					break;
				default:
					$birthdays = '<p>A very happy birthday to:</p><ul><li>' . implode( '</li><li>', $wordpress_array ) . '</li></ul>';
			}
		} else {
			// If no one has a birthday, whomp whomp
			switch ( $format ) {
				case 'tweet':
					$birthdays = false;
					break;
				default:
					$birthdays = '<p>No one has a birthday today. Who knew?</p>';
			}
		}

		$return = array(
			'date'      => $date,
			'birthdays' => $birthdays,
		);

		return $return;
	}
}
new LWTV_OTD_JSON();
