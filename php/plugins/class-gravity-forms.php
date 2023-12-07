<?php
/*
 * Gravity Forms for WordPress hooks
 * @package lwtv-plugin
 */

namespace LWTV\Plugins;

use LWTV\Plugins\Gravity_Forms\Stop_Spammers;

class Gravity_Forms {

	public function __construct() {
		// https://docs.gravityforms.com/gform_disable_view_counter/#1-disable-for-all-forms
		add_filter( 'gform_disable_view_counter', '__return_true' );

		new Stop_Spammers();

		// Call addon code.
		if ( method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			require_once 'gravity-forms/class-gf-approvals.php';
		}
	}
}
