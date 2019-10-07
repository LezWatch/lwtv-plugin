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

		switch ( $when ) {
			case 'today':
				$interval_date = date( 'Y-m-d', time() );
				break;
		}

		$interval_start = $interval_date . ' 00:00:01';
		$interval_end   = $interval_date . ' 23:59:00';
		$events         = $ical->eventsFromInterval( $interval_start, $interval_end );

		return $events;
	}

}

new LWTV_ICS_Parser();
