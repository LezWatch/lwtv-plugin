<?php
/*
Description: REST-API: What's On?

The code that runs the What's On TV API service
- What's On: Outputs what's on TV today

Version: 2.0
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
	 *   - /lwtv/v1/whats-on/show
	 */
	public function rest_api_init() {
		register_rest_route(
			'lwtv/v1',
			'/whats-on/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback_dayname' ),
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/whats-on/(?P<when>[a-zA-Z0-9-]+)',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback_dayname' ),
			)
		);

		register_rest_route(
			'lwtv/v1',
			'/whats-on/(?P<date>[\d]{4}-[\d]{2}-[\d]{2})',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback_date' ),
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/whats-on/show/(?P<name>[a-zA-Z0-9-]+)',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback_show' ),
			)
		);
	}

	/**
	 * Rest API Callback: What's on NOW?
	 */
	public static function rest_api_callback_dayname( $data ) {
		$params = $data->get_params();
		$when   = ( isset( $params['when'] ) && '' !== $params['when'] ) ? sanitize_title_for_query( $params['when'] ) : 'today';

		$response = self::whats_on_dayname( $when );

		return $response;
	}

	/**
	 * Rest API Callback: What's on DATE?
	 */
	public static function rest_api_callback_date( $data ) {
		$params = $data->get_params();

		if ( isset( $params['when'] ) && '' !== $params['when'] && preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $params['when'] ) ) {
			$tz        = 'America/New_York';
			$timestamp = time();
			$dt        = new DateTime( 'now', new DateTimeZone( $tz ) );
			$dt->setTimestamp( $timestamp );
			$datetime = $dt->createFromFormat( 'Y-m-d', $params['when'] );
			$response = $this->whats_on_date( $datetime );
		} else {
			// If there's no valid date, we assume today
			$response = $this->whats_on_dayname( 'today' );
		}

		return $response;
	}

	/**
	 * Rest API Callback: WHEN is a show on?
	 */
	public static function rest_api_callback_show( $data ) {
		$params = $data->get_params();
		$show   = ( isset( $params['name'] ) && '' !== $params['name'] ) ? sanitize_title_for_query( $params['name'] ) : 'unknown';

		$response = $this->whats_on_show( $show );

		return $response;
	}

	/*
	 * What's On DayName
	 *
	 * This is good for named days (today, tomorrow, etc)
	 */
	public static function whats_on_dayname( $when = 'today' ) {

		$when_today = array( 'today', 'now', 'tonight' );
		$when       = ( in_array( $when, $when_today, true ) ) ? 'today' : $when;
		$when_array = array( 'today', 'tomorrow' );
		$lwtv_tz    = new DateTimeZone( 'America/New_York' );

		if ( ! in_array( $when, $when_array, true ) && ! LWTV_Functions::validate_date( $when ) ) {
			$whats_on = 'I may be good, but I\'m not that good. Please only ask me about today and tomorrow.';
		} else {
			require_once dirname( __DIR__, 1 ) . '/features/ics-parser.php';
			$calendar = LWTV_ICS_Parser::generate_by_date( TV_MAZE, $when );
			$whats_on = $calendar;
		}

		if ( empty( $whats_on ) ) {
			$datetime = new DateTime( $when, $lwtv_tz );
			$when_day = $datetime->format( 'l' );

			$return['none'] = 'Nothing is on TV ' . $when_day . '.';
		} else {
			$return = self::parse_calendar( $whats_on );
		}

		return $return;
	}

	/*
	 * What's On Date
	 *
	 * This is good for dates (eg 2019-11-11)
	 */
	public static function whats_on_date( $when ) {
		require_once dirname( __DIR__, 1 ) . '/features/ics-parser.php';
		$lwtv_tz  = new DateTimeZone( 'America/New_York' );
		$calendar = LWTV_ICS_Parser::generate_by_date( TV_MAZE, $when );
		$whats_on = $calendar;

		if ( empty( $whats_on ) ) {
			$datetime = new DateTime( $when, $lwtv_tz );
			$when_day = $datetime->format( 'l' );

			$return['none'] = 'Nothing is on TV ' . $when_day . '.';
		} else {
			$return = self::parse_calendar( $whats_on );
		}

		return $return;
	}

	/*
	 * WHEN is a show on?
	 */
	public static function whats_on_show( $show = 'unknown' ) {

		$return = 'Our show robots were unable to find a television show with that name.';

		if ( 'unknown' !== $show ) {
			$show_obj = get_page_by_path( $show, OBJECT, 'post_type_shows' );
			if ( $show_obj ) {
				$show_id   = $show_obj->ID;
				$show_name = get_the_title( $show_id );

				// Default reply
				$return = 'There is no upcoming airing of ' . $show_name . ' in the next 30 days.';

				// Timestamp things
				$lwtv_tz   = new DateTimeZone( 'America/New_York' );
				$timestamp = time();
				$dt        = new DateTime( 'now', $lwtv_tz );
				$dt->setTimestamp( $timestamp );

				// Get a list of all the shows
				require_once dirname( __DIR__, 1 ) . '/features/ics-parser.php';
				$calendar = LWTV_ICS_Parser::generate_by_date( TV_MAZE, 'full' );

				// Make sure we have anything on TV in the next 30 days
				if ( ! empty( $calendar ) ) {
					$whats_on = self::parse_calendar( $calendar );

					// See if anything on the array is the show we want.
					foreach ( $whats_on as $key => $val ) {
						if ( $val['show'] === $show_name ) {
							// There is/are X upcoming airing(s) of SHOWNAME in the next 30 days. The first is DAY at TIME.
							// Translators: $val['count'] is the number of episodes.
							$episodes = _n( 'is %s upcoming airing', 'are %s upcoming airings', $val['count'] );
							// THIS DATE ISN"T WORKING
							$datetime = $dt->createFromFormat( 'Y-m-d', $val['rawdate'] );
							$return   = 'There ' . $episodes . ' of ' . $show_name . ' in the next 30 days. The first will air on ' . $datetime;
						}
					}
				}
			}
		}

		return $return;

	}

	/**
	 * Parse Calendar and clean up
	 * @param  array $whats_on calendar output
	 * @return array           cleaned up content
	 */
	public static function parse_calendar( $whats_on ) {

		$lwtv_tz   = new DateTimeZone( 'America/New_York' );
		$tvmaze_tz = new DateTimeZone( 'UTC' );
		$on_array  = array();

		foreach ( $whats_on as $episode ) {
			$showtime  = new DateTime( $episode->dtstart, $tvmaze_tz );
			$timestamp = $showtime->getTimestamp();
			$offset    = $lwtv_tz->getOffset( $showtime );
			$interval  = DateInterval::createFromDateString( (string) $offset . 'seconds' );
			$showtime->add( $interval );

			// Reformat the show name and episode name
			$show_name      = substr( $episode->summary, 0, strpos( $episode->summary, ':' ) );
			$episode_number = trim( substr( $episode->summary, strpos( $episode->summary, ':' ) + 1 ) );

			if ( array_key_exists( $show_name, $on_array ) ) {
				if ( ! is_array( $on_array[ $show_name ]['title'] ) ) {
					$on_array[ $show_name ]['title'] = array( $on_array[ $show_name ]['title'] );
				}
				$on_array[ $show_name ]['count']++;
				$on_array[ $show_name ]['show']  = $show_name;
				$on_array[ $show_name ]['title'] = $on_array[ $show_name ]['count'] . ' episodes';
			} else {
				$on_array[ $show_name ] = array(
					'show'    => $show_name,
					'title'   => $episode->description . ' (' . $episode_number . ')',
					'airtime' => $showtime->format( 'H:i' ),
					'rawdate' => $timestamp,
					'count'   => 1,
				);
			}
		}

		foreach ( $on_array as $is_on ) {
			$return[] = $is_on;
		}

		return $return;
	}

}

new LWTV_Whats_On_JSON();
