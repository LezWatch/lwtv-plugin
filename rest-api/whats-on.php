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
	}

	/**
	 * Rest API Callback
	 */
	public static function rest_api_callback( $data ) {
		$params = $data->get_params();
		$when   = ( isset( $params['when'] ) && '' !== $params['when'] ) ? sanitize_title_for_query( $params['when'] ) : 'today';

		$response = $this->whats_on( $when );

		return $response;
	}

	/*
	 * What's On
	 */
	public static function whats_on( $when = 'today' ) {

		$when_today = array( 'today', 'now', 'tonight' );
		$when       = ( in_array( $when, $when_today ) ) ? 'today' : $when;
		$when_array = array( 'today', 'tomorrow' );

		if ( ! in_array( $when, $when_array ) && ! LWTV_Functions::validate_date( $when ) ) {
			$whats_on = 'I may be good, but I\'m not that good. Please only ask me about today and tomorrow.';
		} else {
			require_once dirname( __DIR__, 1 ) . '/features/ics-parser.php';
			$calendar = LWTV_ICS_Parser::generate( TV_MAZE, $when );
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

				$return[] = array(
					'episode' => $episode->summary,
					'title'   => $episode->description,
					'airdate' => $showtime->format( 'Y-m-d H:i:s' ) . ' America/New_York',
					'rawdate' => $timestamp,
				);
			}
		}

		return $return;

	}

}

new LWTV_Whats_On_JSON();
