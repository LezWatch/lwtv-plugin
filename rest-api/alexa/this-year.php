<?php
/*
Description: REST-API - Alexa Skills - This Year

Gives you an idea how this year is going...

Version: 1.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Alexa_This_Year
 */
class LWTV_Alexa_This_Year {


	/**
	 * what_happened function.
	 *
	 * @access public
	 * @param bool $date (default: false)
	 * @return void
	 */
	public function what_happened( $date = false ) {

		// Create the date with regards to timezones
		$tz        = 'America/New_York';
		$timestamp = time();
		$dt        = new DateTime( 'now', new DateTimeZone( $tz ) ); //first argument "must" be a string
		$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp

		$date  = ( false === $date ) ? $dt->format( 'Y-m-d' ) : $date;
		$today = ( $date !== $dt->format( 'Y-m-d' ) ) ? false : true;

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
		if ( $datetime->format( 'Y' ) > date( 'Y' ) ) {
			$datetime->modify( '-1 year' );
			$date = date( 'Y' );
		}

		// Get the data
		$count_array = LWTV_What_Happened_JSON::what_happened( $date );

		// Language of Death
		$dead = 'Miraculously, no characters died';
		if ( $count_array['dead'] > 0 ) {
			// Translators: %s is number of dead characters
			$dead = sprintf( _n( '%s character died', '%s characters died', $count_array['dead'] ), $count_array['dead'] );
		}
		// Now to personalize it...
		if ( $count_array['dead'] > 20 ) {
			$dead = 'Disturbingly, ' . $dead;
		} elseif ( $count_array['dead'] > 30 ) {
			$dead = 'Distressingly, ' . $dead;
		} elseif ( $count_array['dead'] > 40 ) {
			$dead = 'Horrifyingly, ' . $dead;
		}

		// Language of shows, characters, and posts
		// Translators: %s is number of dead characters
		$characters = ( 0 === $count_array['characters'] ) ? 'no characters' : sprintf( _n( '%s character', '%s characters', $count_array['characters'] ), $count_array['characters'] );

		// Translators: %s is number of dead characters
		$shows = ( 0 === $count_array['shows'] ) ? 'no shows' : sprintf( _n( '%s show', '%s shows', $count_array['shows'] ), $count_array['shows'] );

		// Translators: %s is number of dead characters
		$posts = ( 0 === $count_array['posts'] ) ? 'no posts' : sprintf( _n( '%s post', '%s posts', $count_array['posts'] ), $count_array['posts'] );

		// Language sucks...
		if ( $today ) {
			$intro = 'Today ';
		} else {
			switch ( $format ) {
				case 'day':
					$intro = 'On ' . $datetime->format( 'l, F jS, Y' );
					break;
				case 'month':
					$intro = 'In ' . $datetime->format( 'F Y' );
					break;
				default:
					$intro  = ( $datetime->format( 'Y' ) === date( 'Y' ) ) ? 'So far, in ' : 'In ';
					$intro .= $datetime->format( 'Y' );
					break;
			}
		}

		// This Year On Air information:
		// Translators: %s is number of shows
		$on_the_air = ( 0 === $count_array['on_air']['current'] ) ? 'no shows' : sprintf( _n( '%s show', '%s shows', $count_array['on_air']['current'] ), $count_array['on_air']['current'] );

		// Translators: %s is number of shows
		$started = ( 0 === $count_array['on_air']['started'] ) ? 'no shows' : sprintf( _n( '%s show', '%s shows', $count_array['on_air']['started'] ), $count_array['on_air']['started'] );

		// Translators: %s is number of shows
		$ended = ( 0 === $count_array['on_air']['ended'] ) ? 'no shows' : sprintf( _n( '%s show', '%s shows', $count_array['on_air']['ended'] ), $count_array['on_air']['ended'] );

		// This Year DEATH information
		$death_this_year_query = LWTV_Loops::post_meta_and_tax_query( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'Y' ), 'lez_cliches', 'slug', 'dead', 'REGEXP' );

		// Translators: %s is number of dead characters
		$death_this_year = ( 0 === $death_this_year_query->post_count ) ? 'no characters died' : sprintf( _n( '%s character died', '%s characters died', $death_this_year_query->post_count ), $death_this_year_query->post_count );

		// Conclusion
		if ( $datetime->format( 'Y' ) > 2013 ) {
			$output = $intro . ', LezWatch T. V. made ' . $posts . ', added ' . $characters . ', and added ' . $shows . '. ' . $dead . '.';
		} else {
			$output = 'Looking at the history of queer female, non-binary, and trans characters on television, I can tell you some things about ' . $datetime->format( 'Y' ) . ' ... ' . $dead . '.';
		}

		// We always give a year overview
		$output .= ' For ' . $datetime->format( 'Y' ) . ' as a whole, there were ' . $on_the_air . ' with queer female, non-binary, or trans characters on the air. ' . $started . ' started and ' . $ended . ' ended.';

		if ( 'year' !== $format ) {
			$output .= ' Overall ' . $death_this_year . ' that year.';
		}

		return $output;
	}

}

new LWTV_Alexa_This_Year();
