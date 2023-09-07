<?php
/*
 * Transients
 *
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Transients {

	public function __construct() {
		// Custom search.
		add_action( 'init', array( $this, 'get_transient' ) );
	}

	/**
	 * Get Transient
	 *
	 * A wrapper to default to false if you're developing.
	 */
	public static function get_transient( $transient ) {
		if ( defined( 'LWTV_DEV_SITE' ) && LWTV_DEV_SITE ) {
			return false;
		}

		return get_transient( $transient );
	}
}

new LWTV_Transients();
