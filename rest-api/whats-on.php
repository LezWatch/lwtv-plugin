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
	 *   - /lwtv/v1/whats-on/[today|tomorrow|week|month|year]
	 *   - /lwtv/v1/whats-on/show/YYYY-MM-DD
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
			'/whats-on/(?P<when>[a-zA-Z0-9-]+)',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback' ),
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/whats-on/(?P<when>[a-zA-Z0-9-]+)/(?P<name>[a-zA-Z0-9-]+)',
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
		$show   = ( isset( $params['name'] ) && '' !== $params['name'] ) ? sanitize_title_for_query( $params['name'] ) : false;
		$date   = false;

		// Check if there's a valid date
		if ( preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $params['name'] ) ) {
			$date = $params['name'];
		}

		// Sometimes when comes in as a date, and if so, it's a date call.
		if ( preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $when ) ) {
			$date = $params['when'];
			$when = 'date';
		}

		// Figure out what we're running...
		switch ( $when ) {
			case 'date':
				$response = $this->whats_on_date( $date );
				break;
			case 'show':
				$response = $this->whats_on_show( $show );
				break;
			case 'today':
			case 'tomorrow':
			case 'tonight':
			case 'now':
				$response = self::whats_on_dayname( $when );
				break;
			case 'week':
				$response = $this->whats_on_week( $date );
				break;
		}

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
			$datetime       = new DateTime( $when, $lwtv_tz );
			$when_day       = $datetime->format( 'l' );
			$return['none'] = 'Nothing is on TV this ' . $when_day . '.';
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
	public static function whats_on_date( $date ) {

		require_once dirname( __DIR__, 1 ) . '/features/ics-parser.php';
		$lwtv_tz  = new DateTimeZone( 'America/New_York' );
		$calendar = LWTV_ICS_Parser::generate_by_date( TV_MAZE, 'date', $date );
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
	 * What's On Week
	 *
	 * This is good for whole weeks (eg 2019-11-11)
	 */
	public static function whats_on_week( $when = 'now' ) {
		require_once dirname( __DIR__, 1 ) . '/features/ics-parser.php';
		$lwtv_tz = new DateTimeZone( 'America/New_York' );

		if ( 'now' === $when ) {
			$calendar = LWTV_ICS_Parser::generate_by_date( TV_MAZE, 'week' );
		} else {
			$calendar = LWTV_ICS_Parser::generate_by_date( TV_MAZE, 'week', $when );
		}

		$whats_on = $calendar;

		if ( empty( $whats_on ) ) {
			$datetime = new DateTime( $when, $lwtv_tz );
			$when_day = $datetime->format( 'l' );

			$return['none'] = 'Nothing is on TV that week. We\'re pretty shocked too!';
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
							// Translators: $val['count'] is the number of episodes.
							$episodes = sprintf( _n( 'is %s upcoming airing', 'are %s upcoming airings', $val['count'] ), $val['count'] );

							// Convert the date
							$datetime = $dt->createFromFormat( 'U', $val['rawdate'] );

							// Translators: $val['count'] is the number of episodes.
							$will_air = _n( 'It', 'The first', $val['count'] );

							$return = array(
								'pretty' => 'There ' . $episodes . ' of "' . $show_name . '" in the next 30 days. ' . $will_air . ' will air on ' . $datetime->format( 'M d' ) . ' at ' . $val['airtime'] . ' US Eastern.',
								'simple' => $datetime->format( 'M d' ) . ' at ' . $val['airtime'] . ' US Eastern.',
							);
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

		if ( empty( $whats_on ) || ! is_array( $whats_on ) ) {
			$return = 'Error: The calendar is empty.';
		} else {
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
		}

		return $return;
	}

}

new LWTV_Whats_On_JSON();
