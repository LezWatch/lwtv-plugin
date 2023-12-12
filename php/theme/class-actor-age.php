<?php

namespace LWTV\Theme;

class Actor_Age {
	/**
	 * Generate Actor Age
	 *
	 * Take the birth and death and ouput as needed.
	 *
	 * @param string  $actor  ID Actor ID
	 *
	 * @return string Age.
	 */
	public function make( $actor_id ) {
		$output = '';
		$end    = new \DateTime();
		if ( get_post_meta( $actor_id, 'lezactors_death', true ) ) {
			$end = new \DateTime( get_post_meta( $actor_id, 'lezactors_death', true ) );
		}
		if ( get_post_meta( $actor_id, 'lezactors_birth', true ) ) {
			$start = new \DateTime( get_post_meta( $actor_id, 'lezactors_birth', true ) );
		}
		if ( isset( $start ) ) {
			$output = $start->diff( $end );
		}

		return $output;
	}
}
