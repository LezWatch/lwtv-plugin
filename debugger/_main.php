<?php
/*
 * Debugging Tools for weird content
 *
 * @since 2.4
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Debug {

	/**
	 * Sanitize social media handles
	 * @param  string $string Username
	 * @param  string $for    Social Media Type
	 * @return string         sanitized username
	 */
	public function sanitize_social( $string, $for ) {

		// Defaults.
		$trim  = 10;
		$regex = '/[^a-zA-Z_.0-9]/';

		switch ( $for ) {
			case 'instagram': // ex: https://instagram.com/lezwatchtv
				$string = str_replace( 'https://instagram.com/', '', $string );
				$trim   = 30;
				break;
			case 'twitter': // ex: https://twitter.com/lezwatchtv
				$string = str_replace( 'https://twitter.com/', '', $string );
				$trim   = 15;
				break;
			case 'mastodon': // ex: https://mstdn.social/@lezwatchtv
				$regex = '/[^a-zA-Z_.0-9:\/@]/';
				$trim  = 2000;
				break;
		}

		// Remove all illegal characters.
		$clean = preg_replace( $regex, '', trim( $string ) );

		$clean = substr( $clean, 0, $trim );

		return $clean;
	}

	/**
	 * Clean up the WikiDate
	 * @param  string $date Wiki formatted date: +1968-07-07T00:00:00Z
	 * @return string      LezWatch formatted date: 1968-07-07
	 */
	public function format_wikidate( $date ) {
		$clean = trim( substr( $date, 0, strpos( $date, 'T' ) ), '+' );
		return $clean;
	}

	/**
	 * Validate IMDB
	 * @param  string  $string IMDB ID
	 * @return boolean         true/false
	 */
	public function validate_imdb( $string, $type = 'show' ) {

		// Defaults
		$result = true;
		$type   = ( ! in_array( $type, array( 'show', 'actor' ), true ) ) ? 'show' : $type;

		switch ( $type ) {
			case 'show':
				$substr = 'tt';
				break;
			case 'actor':
				$substr = 'nm';
				break;
		}

		if ( ! isset( $substr ) ) {
			$result = false;
		} elseif ( substr( $string, 0, 2 ) === $substr && ! is_numeric( substr( $string, 2 ) ) ) {
			// IMDB looks like tt123456 or nm12356
			$result = false;
		}

		return $result;
	}

}

new LWTV_Debug();

require_once 'actors.php';
require_once 'characters.php';
require_once 'shows.php';
require_once 'queers.php';
