<?php
/*
Description: ICS Parser

https://github.com/u01jmg3/ics-parser

Version: 1.2
*/

namespace LWTV\Calendar;

// Include iCal parser
require_once 'ICal/ICal.php';
require_once 'ICal/Event.php';

use ICal\ICal;

class ICS_Parser {

	/**
	 * Construct
	 * Runs the Code
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Main Plugin setup
	 *
	 * Adds actions, filters, etc. to WP
	 *
	 * @access public
	 * @return void
	 * @since 1.2
	 */
	public function init() {
		// Plugin requires permalink usage - Only setup handling if permalinks are enabled
		if ( '' !== get_option( 'permalink_structure' ) ) {
			// tell WP not to override query vars
			add_filter( 'query_vars', array( $this, 'query_vars' ) );
		}
	}

	/**
	 * Add the query variables so WordPress won't override it
	 *
	 * @return $vars
	 * @since 1.2
	 */
	public function query_vars( $vars ) {
		$vars[] = 'tvdate';
		return $vars;
	}

	/**
	 * Generate what's on for a specific date
	 * @param  string $url  URL of calendar
	 * @param  string $when string of a day [today, tomorrow]
	 * @param  string $date date event happens [Y-m-d]
	 * @return array        array of all the shows on that day
	 */
	public function generate_by_date( $url, $when, $date = false ) {
		$ical = new ICal();
		$ical->initUrl( $url );

		// Timezone
		$tz = new \DateTimeZone( 'America/New_York' );

		// Default is today:
		$start_datetime = new \DateTime( 'today', $tz );

		switch ( $when ) {
			case 'date':
				if ( ! $date || 'today' === $date || ! self::validate_date( $date ) ) {
					$date = $start_datetime->format( 'Y-m-d' );
				}

				$start_datetime = \DateTime::createFromFormat( 'Y-m-d', $date, $tz );
				$end_datetime   = \DateTime::createFromFormat( 'Y-m-d', $date, $tz );
				$end_datetime->modify( '+1 day' );
				break;
			case 'full':
				$end_datetime = new \DateTime( 'today + 30days', $tz );
				break;
			case 'today':
				$end_datetime = new \DateTime( 'today + 1day', $tz );
				break;
			case 'tomorrow':
				$start_datetime = new \DateTime( 'tomorrow', $tz );
				$end_datetime   = new \DateTime( 'tomorrow + 1day', $tz );
				break;
			case 'week':
				// If the week has no date, it's this week
				if ( ! $date || 'today' === $date || ! self::validate_date( $date ) ) {
					$start_datetime = new \DateTime( 'today', $tz );
					if ( 'Sun' !== $start_datetime->format( 'D' ) ) {
						$start_datetime = new \DateTime( 'last Sunday', $tz );
						$end_datetime   = new \DateTime( 'last Sunday', $tz );
					}
					$end_datetime->modify( '+1 week' );
				} else {
					$start_dt       = \DateTime::createFromFormat( 'Y-m-d', $date, $tz );
					$start_datetime = $start_dt;
					$end_dt         = \DateTime::createFromFormat( 'Y-m-d', $date, $tz );
					$end_datetime   = $end_dt;

					if ( 'Sun' !== $start_dt->format( 'D' ) ) {
						$start_datetime->modify( 'Sunday' );
						$end_datetime->modify( 'Sunday' );
					}

					$end_datetime->modify( '+1 week' );
				}
				break;
		}

		// We have to be off by 5 hours because of UTC and New York.
		$interval_start = $start_datetime->format( 'Y-m-d' ) . ' 05:00:00';
		$interval_end   = $end_datetime->format( 'Y-m-d' ) . ' 04:59:00';
		$events         = $ical->eventsFromRange( $interval_start, $interval_end );

		return $events;
	}

	/**
	 * validate date format
	 * @param  string  $date   The date we're checking
	 * @param  string  $format Format of the date (default Y-m-d)
	 * @return boolean         True/false
	 */
	public function validate_date( $date, $format = 'Y-m-d' ) {
		$d = \DateTime::createFromFormat( $format, $date );
		return $d && $d->format( $format ) === $date;
	}
}
