<?php
/**
 * class LWTV_Queery_Is_Show_On_Air
 *
 * @since 5.0
 */

class LWTV_Queery_Is_Show_On_Air {
	/**
	 * Determine if a show is on air
	 *
	 * @access public
	 * @param  int  $post_id - Post ID
	 * @param  int  $year    - Year they may be on air
	 * @return bool
	 */
	public function make( $post_id, $year ) {

		// Defaults
		$return    = false;
		$this_year = gmdate( 'Y' );

		// Get the data.
		if ( get_post_meta( $post_id, 'lezshows_airdates', true ) ) {
			$airdates = get_post_meta( $post_id, 'lezshows_airdates', true );
			// If the start is 'current' make it this year (though it really never should be.)
			if ( 'current' === $airdates['start'] ) {
				$airdates['start'] = $this_year;
			}

			// Setting 'end' to current for easier math later
			if ( 'current' === $airdates['finish'] ) {
				$airdates['finish'] = $this_year;
			}
		}

		if ( isset( $airdates ) ) {
			// if START is equal to or LESS than $year
			// AND if END is qual to or GREATER than $year
			// Then the show was on air.
			if ( $airdates['start'] <= $year && $airdates['finish'] >= $year ) {
				$return = true;
			}
		}

		return $return;
	}
}
