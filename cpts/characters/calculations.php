<?php
/**
 * Name: Character Calculations
 * Description: Calculate various data points for characters
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Characters_Calculate
 *
 * @since 3.0
 */

class LWTV_Characters_Calculate {

	/**
	 * Does the Math
	 * @param  int $post_id Post ID of character
	 * @return n/a
	 */
	public function do_the_math( $post_id ) {
		// Calculate Death
		self::death( $post_id );
	}

	/**
	 * Calculate the most recent death
	 * @param  int   $post_id
	 * @return N/A   No return, just update
	 */
	public function death( $post_id ) {
		// get the most recent death and save it as a new meta
		$character_death = get_post_meta( $post_id, 'lezchars_death_year', true );
		$last_char_death = get_post_meta( $post_id, 'lezchars_last_death', true );
		$newest_death    = '0000-00-00';
		if ( '' !== $character_death ) {
			foreach ( $character_death as $death ) {
				if ( $death > $newest_death ) {
					$newest_death = $death;
				}
			}
			// If there's a newest death AND it isn't equal last death, save it
			if ( '0000-00-00' !== $newest_death && $newest_death !== $last_char_death ) {
				update_post_meta( $post_id, 'lezchars_last_death', $newest_death );
			}
		}
	}

}

new LWTV_Characters_Calculate();
