<?php
/*
Description: ICS Parser

https://github.com/u01jmg3/ics-parser

Version: 1.2
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

use ICal\ICal;

/**
 * class LWTV_ICS_Parser
 *
 */
class LWTV_ICS_Parser {

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
	public static function generate_by_date( $url, $when, $date = false ) {
		$ical = new ICal();
		$ical->initUrl( esc_url( $url ) );

		$tz = new DateTimeZone( 'America/New_York' );
		$dt = new DateTime( 'today', $tz );

		// Default is today:
		$start_datetime = $dt;

		switch ( $when ) {
			case 'date':
				$start_datetime = DateTime::createFromFormat( 'Y-m-d', $date );
				$start_datetime->setTimeZone( $tz );
				$end_datetime = DateTime::createFromFormat( 'Y-m-d', $date );
				$end_datetime->setTimeZone( $tz );
				$end_datetime->modify( '+1 day' );
				break;
			case 'full':
				$end_datetime = new DateTime( 'today + 30days', $tz );
				break;
			case 'today':
				$end_datetime = new DateTime( 'today + 1day', $tz );
				break;
			case 'tomorrow':
				$start_datetime = new DateTime( 'tomorrow', $tz );
				$end_datetime   = new DateTime( 'tomorrow + 1day', $tz );
				break;
			case 'week':
				// If the week has no date, it's this week
				if ( ! $date ) {
					$start_datetime = new DateTime( 'today', $tz );
					if ( 'Sun' !== $dt->format( 'D' ) ) {
						$start_datetime = new DateTime( 'last Sunday', $tz );
						$end_datetime   = new DateTime( 'last Sunday', $tz );
					}

					$end_datetime->modify( '+1 week' );
				} else {
					$start_dt = DateTime::createFromFormat( 'Y-m-d', $date );
					$start_dt->setTimeZone( $tz );
					$start_datetime = $start_dt;

					$end_dt = DateTime::createFromFormat( 'Y-m-d', $date );
					$end_dt->setTimeZone( $tz );
					$end_datetime = $end_dt;

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

}

new LWTV_ICS_Parser();
