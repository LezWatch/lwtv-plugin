<?php
/*
Description: REST-API - Alexa Skills - What's On TV

Version: 1.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Alexa_Whats_On
 */
class LWTV_Alexa_Whats_On {

	public function on_a_day( $date = 'today' ) {

		// Right now, everything is 'today'
		$date      = 'today';
		$timestamp = strtotime( 'now' );

		// Get the list of what's on:
		$data = LWTV_Whats_On_JSON::whats_on( $date );

		$count    = ( 'none' === key( $data ) ) ? 0 : count( $data );

		if ( $count > 0 ) {
			$how_many  = $count . ' ' . _n( 'show', 'shows', $count ) . ' are on TV today';
			$showcount = 1;
			$episodes  = '';
			foreach ( $data as $one_show ) {
				if ( $showcount === $count && 1 !== $count ) {
					$episodes .= 'And ';
				}
				$eptime    = $one_show['rawdate'];
				$episodes .= $one_show['episode'] . ' at ' . date( 'g:i A', $eptime + 5 * 3600 ) . '. ';
				$showcount++;
			}
		}
		$output = $how_many . ' on ' . date( 'F jS', $timestamp ) . '. ' . $episodes . ' All times are US Eastern.';

		return $output;
	}

}

new LWTV_Alexa_Whats_On();