<?php
/*
Description: ICS Parser

https://github.com/u01jmg3/ics-parser

Version: 1.0
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

	public static function generate( $url, $when ) {
		$ical = new ICal();
		$ical->initUrl( esc_url( $url ) );

		$tz = new DateTimeZone( 'America/New_York' );

		switch ( $when ) {
			case 'today':
				$start_datetime = new DateTime( 'today', $tz );
				$end_datetime   = new DateTime( 'today + 1day', $tz );
				break;
			case 'tomorrow':
				$start_datetime = new DateTime( 'tomorrow', $tz );
				$end_datetime   = new DateTime( 'tomorrow + 1day', $tz );
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
