<?php
/**
 * Deprecated LWTV Affiliates because ads was a losing game and was worthless. May be worth revisiting, but it will
 * be something totally different, like just "Ads" or whatever.
 *
 * No sniffing this one.
 */

// phpcs:disable

class LWTV_Affilliates {

	/**
	 * Actors.
	 *
	 * Used to return an ad.
	 *
	 * @param number $id     - Post ID
	 * @param string $format - Format of ad
	 *
	 * @return null
	 */
	public function actors( $id, $format ) {
		return '';
	}

	/**
	 * Characters
	 *
	 * Used to return an ad.
	 *
	 * @param number $id     - Post ID
	 * @param string $format - Format of ad
	 *
	 * @return null
	 */
	public function characters( $id, $format ) {
		return '';
	}


	/**
	 * Shows
	 *
	 * Used to return an ad OR ways to watch.
	 *
	 * Advertisements - removed.
	 * Ways To Watch  - Replaced with LWTV_Ways_to_watch->ways_to_watch();
	 *
	 * @param number $id - Post ID
	 * @param string $format - Format of ad
	 *
	 * @return mixed - Either Ways to watch (string) or null.
	 */
	public function shows( $id, $format ) {
		if ( 'affiliate' === $format ) {
			return ( new LWTV_Ways_To_Watch() )->ways_to_watch( $id );
		}

		return '';
	}

}

new LWTV_Affilliates();
