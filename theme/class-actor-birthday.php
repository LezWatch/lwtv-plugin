<?php

class LWTV_Theme_Actor_Birthday {
	/**
	 * Is today a birthday?
	 *
	 * @access public
	 *
	 * @param  string $the_id
	 * @return bool
	 */
	public function make( $the_id ) {
		$today_is = gmdate( 'm-d' );
		$birthday = substr( get_post_meta( $the_id, 'lezactors_birth', true ), 5 );
		if ( $birthday === $today_is ) {
			return true;
		}

		return false;
	}
}
