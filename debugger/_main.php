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

		$clean = preg_replace( '/[^a-zA-Z_.0-9]/', '', trim( $string ) );

		switch ( $for ) {
			case 'instagram':
				$trim = 30;
				break;
			case 'twitter':
				$trim = 15;
				break;
			default:
				$trim = 10;
		}

		$clean = substr( $clean, 0, $trim );

		return $clean;
	}

	/**
	 * Clean up the WikiDate
	 * @param  string $date Wikiformated date: +1968-07-07T00:00:00Z
	 * @return string      LezWatch formated date: 1968-07-07
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
	public function validate_imdb( $string ) {

		$result = true;

		// IMDB looks like tt123456 or nm12356
		if ( substr( $string, 0, 2 ) === 'nm' || substr( $string, 0, 2 ) === 'tt' ) {
			if ( ! is_numeric( substr( $string, 2 ) ) ) {
				$result = false;
			}
		} else {
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
