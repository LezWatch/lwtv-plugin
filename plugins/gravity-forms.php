<?php
/*
 * Gravity Forms for WordPress hooks
 * @package lwtv-plugin
 */

class LWTV_Gravity_Forms {

	public function __construct() {
		// https://docs.gravityforms.com/gform_disable_view_counter/#1-disable-for-all-forms
		add_filter( 'gform_disable_view_counter', '__return_true' );

		// Include add-ons
		require_once 'gravity-forms/stop-spammers.php';

		if ( method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			require_once 'gravity-forms/class-gf-approvals.php';
		}
	}
}

new LWTV_Gravity_Forms();
