<?php
/*
Description: REST-API - Alexa Skills - Bury Your Queers

Since Amazon keeps flagging this as 'hate speech' we're rebranding.

Version: 1.0
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Alexa_BYQ
 */
class LWTV_Alexa_BYQ {

	/**
	 * How many characters died.
	 * 
	 * @access public
	 * @param string $type (default: 'simple')
	 * @param string $date (default: date('Y'))
	 * @return string
	 */
	public function how_many( $type = 'simple', $date = date('Y') ) {	
		// Simple - how many have died total
		if ( $type == 'simple' ) {
			$data   = LWTV_Stats_JSON::statistics( 'death', 'simple' );
			$output = 'A total of '. $data['characters']['dead'] .' queer female characters have died on TV.';
		} 
		
		// How many died in a year
		if ( $type == 'year' ) {
			$data     = LWTV_Stats_JSON::statistics( 'death', 'years' );
			$count    = $data[$date]['count'];
			$how_many = 'No queer female characters died on TV in ' . $date . '.';
			if ( $count > 0 ) {
				$how_many = $count .' queer female ' . _n( 'character', 'characters', $count ) . ' died on TV in ' . $date . '.';
			}
			$output = $how_many;
		}
		
		return $output;
	}

	public function on_a_day( $timestamp = false ) {

		// Makesure we have a default timestamp
		$timestamp  = ( $timestamp == false )? time() : $timestamp ;

		// Figure out who died on a day...
		$this_day = date('m-d', $timestamp );
		$data     = LWTV_BYQ_JSON::on_this_day( $this_day );
		$count    = ( key( $data ) == 'none' )? 0 : count( $data ) ;
		$how_many = 'No queer females died';
		$the_dead = '';
		if ( $count > 0 ) {
			$how_many  = $count . ' queer female ' . _n( 'character', 'characters', $count ) . ' died';
			$deadcount = 1;
			foreach ( $data as $dead_character ) {
				if ( $deadcount == $count && $count !== 1 ) $the_dead .= 'And ';
				$the_dead .= $dead_character['name'] . ' in ' . $dead_character['died'] . '. ';
				$deadcount++;
			}
		}
		$output = $how_many . ' on '. date('F jS', $timestamp ) . '. ' . $the_dead;
		
		return $output;
	}

}

new LWTV_Alexa_BYQ();