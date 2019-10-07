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

	public function generate( $url ) {
		$ical = new ICal();
		$ical->initUrl( esc_url( $url ) );

		return $ical;
	}

}

new LWTV_ICS_Parser();
