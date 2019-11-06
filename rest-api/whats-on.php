<?php
/*
Description: REST-API: What's On?

The code that runs the What's On TV API service
- What's On: Outputs what's on TV today

Version: 1.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Whats_On_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_Whats_On_JSON {

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
	 *   - /lwtv/v1/whats-on/
	 *   - /lwtv/v1/whats-on/today
	 *   - /lwtv/v1/whats-on/week
	 */
	public function rest_api_init() {
		register_rest_route(
			'lwtv/v1',
			'/whats-on/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback' ),
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/whats-on/(?P<when>[a-zA-Z]+)',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback' ),
			)
		);

		register_rest_route(
			'lwtv/v1',
			'/whats-on/show/(?P<show>[a-zA-Z]+)',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback' ),
			)
		);

	}

	/**
	 * Rest API Callback
	 */
	public static function rest_api_callback( $data ) {
		$params = $data->get_params();
		$when   = ( isset( $params['when'] ) && '' !== $params['when'] ) ? sanitize_title_for_query( $params['when'] ) : 'today';
		$show   = ( isset( $params['show'] ) && '' !== $params['show'] ) ? sanitize_title_for_query( $params['show'] ) : false;

		if ( 'show' === $when ) {
			$response = $this->show_on( $show );
		} else {
			$response = $this->whats_on( $when );
		}

		return $response;
	}

	/*
	 * What's On for a day
	 */
	public static function whats_on( $when = 'today' ) {

		$when_today = array( 'today', 'now', 'tonight' );
		$when       = ( in_array( $when, $when_today ) ) ? 'today' : $when;
		$when_array = array( 'today', 'tomorrow' );
		$on_array   = array();

		if ( ! in_array( $when, $when_array ) && ! LWTV_Functions::validate_date( $when ) ) {
			$whats_on = 'I may be good, but I\'m not that good. Please only ask me about today and tomorrow.';
		} else {
			require_once dirname( __DIR__, 1 ) . '/features/ics-parser.php';
			$calendar = LWTV_ICS_Parser::generate_by_date( TV_MAZE, $when );
			$whats_on = $calendar;
		}

		$lwtv_tz   = new DateTimeZone( 'America/New_York' );
		$tvmaze_tz = new DateTimeZone( 'UTC' );

		if ( empty( $whats_on ) ) {
			$datetime = new DateTime( $when, $lwtv_tz );
			$when_day = $datetime->format( 'l' );

			$return['none'] = 'Nothing is on TV ' . $when_day . '.';
		} else {
			foreach ( $whats_on as $episode ) {
				$showtime  = new DateTime( $episode->dtstart, $tvmaze_tz );
				$timestamp = $showtime->getTimestamp();

				$offset     = $lwtv_tz->getOffset( $showtime );
				$interval   = DateInterval::createFromDateString( (string) $offset . 'seconds' );
				$showtime->add( $interval );

				// this needs to loop check if the same show is already listed.
				// If the show is there already, it's probably netflix but we should say
				// X Episodes total instead of list the episodes by name.

				$show_name = substr( $episode->summary, 0, strpos( $episode->summary, ':' ) );

				if ( array_key_exists( $show_name, $on_array ) ) {
					if ( ! is_array( $on_array[ $show_name ]['title'] ) ) {
						$on_array[ $show_name ]['title'] = array( $on_array[ $show_name ]['title'] );
					}
					$on_array[ $show_name ]['episode'] = $show_name . ' (multiple episodes)';
					$on_array[ $show_name ]['title'][] = $episode->description;
				} else {
					$on_array[ $show_name ] = array(
						'episode' => $episode->summary,
						'title'   => $episode->description,
						'airdate' => $showtime->format( 'Y-m-d H:i:s' ) . ' America/New_York',
						'rawdate' => $timestamp,
					);
				}
			}

			foreach ( $on_array as $is_on ) {
				$return[] = $is_on;
			}
		}

		return $return;

	}

	/*
	 * What's On for a show
	 */
	public static function show_on( $show = false ) {
		// Have this spit out when a show is on next.

		$return = false;

		if ( false !== $show ) {
			$show_obj = get_page_by_path( $show, OBJECT, 'post_type_shows' );
			if ( $show_obj ) {
				$show_id = $show_obj->ID;

				// Here we build out when is the show on next?
				// Need to search the ics file
				$next_on = 'DATE';
			}

			// if there's nothing on, lo siento.
			if ( ! isset( $next_on ) ) {
				$return = 'I\'m sorry, there is no upcoming airing of X.';
			}
		}

		if ( ! $return ) {
			return new WP_Error( 'invalid_show', 'Invalid show name.', array( 'status' => 404 ) );
		} else {
			return $return;
		}

	}

}

new LWTV_Whats_On_JSON();
