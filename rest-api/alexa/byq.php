<?php
/*
Description: REST-API - Alexa Skills - Bury Your Queers

Since Amazon keeps flagging this as 'hate speech' we're rebranding.

Version: 1.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Alexa_BYQ
 */
class LWTV_Alexa_BYQ {

	/**
	 * How many characters died.
	 *
	 * @access public
	 * @param string $type (default: 'simple')
	 * @param string $date (default: gmdate('Y'))
	 * @return string
	 */
	public function how_many( $type = 'simple' ) {

		// Simple - how many have died total
		if ( 'simple' === $type ) {
			$data   = ( new LWTV_Rest_API_Stats_JSON() )->statistics( 'death', 'simple' );
			$output = 'A total of ' . $data['characters']['dead'] . ' characters have died on TV.';
		} else {
			$date = $type;
			// Figure out what date we're working with here...
			if ( preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date ) ) {
				$format   = 'day';
				$datetime = DateTime::createFromFormat( 'Y-m-d', $date );
			}
			if ( preg_match( '/^[0-9]{4}-[0-9]{2}$/', $date ) ) {
				$format   = 'month';
				$datetime = DateTime::createFromFormat( 'Y-m', $date );
			}
			if ( preg_match( '/^[0-9]{4}$/', $date ) ) {
				$format   = 'year';
				$datetime = DateTime::createFromFormat( 'Y', $date );
			}

			// If it's the future, be smarter than Alexa...
			if ( $datetime->format( 'Y' ) > gmdate( 'Y' ) ) {
				$datetime->modify( '-1 year' );
			}

			// Calculate death
			switch ( $format ) {
				case 'year':
					$death_query = ( new LWTV_Queery_Post_Meta_And_Tax() )->make( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'Y' ), 'lez_cliches', 'slug', 'dead', 'REGEXP' );
					$death_count = $death_query->post_count;
					break;
				case 'month':
					$death_query      = ( new LWTV_Queery_Post_Meta_And_Tax() )->make( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'Y' ), 'lez_cliches', 'slug', 'dead', 'REGEXP' );
					$death_list_array = ( new LWTV_Rest_API_BYQ() )->list_of_dead_characters( $death_query );
					$death_count      = 0;
					foreach ( $death_list_array as $the_dead ) {
						if ( $datetime->format( 'm' ) === gmdate( 'm', $the_dead['died'] ) ) {
							++$death_count;
						}
					}
					break;
				case 'day':
					$death_query = ( new LWTV_Queery_Post_Meta_And_Tax() )->make( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'm/d/Y' ), 'lez_cliches', 'slug', 'dead', 'REGEXP' );
					$death_count = $death_query->post_count;
					break;
				default:
					$death_count = 0;
			}

			$dead = 'no one died!';
			if ( $death_count > 0 ) {
				// translators: %s number of characters
				$dead = sprintf( _n( '%s character', '%s characters', $death_count ), $death_count );
			}

			// Language sucks
			switch ( $format ) {
				case 'day':
					$intro = 'On ' . $datetime->format( 'l, F jS, Y' );
					break;
				case 'month':
					$intro = 'In ' . $datetime->format( 'F Y' );
					break;
				default:
					$intro  = ( $datetime->format( 'Y' ) === gmdate( 'Y' ) ) ? 'So far in ' : 'In ';
					$intro .= $datetime->format( 'Y' );
					break;
			}

			$output = $intro . ' ' . $dead . ' died.';
		}

		return $output;
	}

	public function on_a_day( $date = false ) {

		// Make sure we have a default timestamp
		$timestamp = ( strtotime( $date ) === false ) ? time() : strtotime( $date );

		// Figure out who died on a day...
		$this_day = gmdate( 'm-d', $timestamp );
		$data     = ( new LWTV_Rest_API_BYQ() )->on_this_day( $this_day );
		$count    = ( 'none' === key( $data ) ) ? 0 : count( $data );
		$how_many = 'No characters died ';
		$the_dead = '';
		if ( $count > 0 ) {
			$how_many  = $count . ' ' . _n( 'character', 'characters', $count ) . ' died';
			$deadcount = 1;
			foreach ( $data as $dead_character ) {
				if ( $deadcount === $count && 1 !== $count ) {
					$the_dead .= 'And ';
				}
				$the_dead .= $dead_character['name'] . ' in ' . $dead_character['died'] . '. ';
				++$deadcount;
			}
		}
		$output = $how_many . ' on ' . gmdate( 'F jS', $timestamp ) . '. ' . $the_dead;

		return $output;
	}
}

new LWTV_Alexa_BYQ();
