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
		$data = ( new LWTV_Whats_On_JSON() )->whats_on_date( $date );

		$count = ( 'none' === key( $data ) ) ? 0 : count( $data );

		if ( $count > 0 ) {
			$how_many   = $count . ' ' . _n( 'show', 'shows', $count ) . ' are on TV today';
			$showcount  = 1;
			$episodes   = '';
			$show_array = array();

			foreach ( $data as $one_show ) {
				if ( $showcount === $count && 1 !== $count ) {
					$episodes .= 'And ';
				}
				$eptime = $one_show['rawdate'];
				// Time is somehow off?
				$episodes .= $one_show['show'] . ' at ' . gmdate( 'g:i A', $eptime + ( 19 * 3600 ) ) . '. ';
				$showcount++;
			}
		}
		$output = $how_many . ' ' . gmdate( 'l F jS', $timestamp ) . '. ' . $episodes . ' All times are US Eastern.';

		return $output;
	}

	/**
	 * What's on show
	 * @param  string $show Slug of the TV show
	 * @return string       Pretty language about what's on
	 */
	public function show( $show ) {
		$data   = ( new LWTV_Whats_On_JSON() )->whats_on_show( $show );
		$output = $data['pretty'];

		return $output;

	}

}

new LWTV_Alexa_Whats_On();
