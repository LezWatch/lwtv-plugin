<?php
/*
Description: ICS Parser

https://github.com/u01jmg3/ics-parser

Version: 1.1
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
	 * Generate what's on for a specific date
	 * @param  string $url  URL of calendar
	 * @param  string $when string of a day [today, tomorrow]
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
			case 'today':
				$end_datetime   = new DateTime( 'today + 1day', $tz );
				break;
			case 'tomorrow':
				$start_datetime = new DateTime( 'tomorrow', $tz );
				$end_datetime   = new DateTime( 'tomorrow + 1day', $tz );
				break;
			case 'full':
				$start_datetime = $dt;
				$end_datetime   = new DateTime( 'today + 30days', $tz );
				break;
			case 'week':
				
				// If the week has no date, it's this week
				if ( ! $date ) {
					if ( 'Sun' === $dt->format( 'D' ) ) {
						$start_datetime   = new DateTime( 'today', $tz );
					} else {
						$start_datetime = new DateTime( 'last Sunday', $tz );
					}
					if ( 'Sat' === $dt->format( 'D' ) ) {
						$end_datetime   = new DateTime( 'today', $tz );
					} else {
						$end_datetime = new DateTime( 'Saturday', $tz );
					}
				} else {
					// ????
				}
				break;
			case 'date':
				// We've passed a custom DAY
				$custom_date    = $date;
				$start_datetime = new DateTime( $custom_date, $tz );
				$end_datetime   = new DateTime( $custom_date . ' + 1day', $tz );
				break;
		}

		// We have to be off by 5 hours because of UTC and New York.
		$interval_start = $start_datetime->format( 'Y-m-d' ) . ' 05:00:00';
		$interval_end   = $end_datetime->format( 'Y-m-d' ) . ' 04:59:00';
		$events         = $ical->eventsFromRange( $interval_start, $interval_end );

		return $events;
	}

	// This
	public static function generate_block() {

		return $return;
	}

}

new LWTV_ICS_Parser();
