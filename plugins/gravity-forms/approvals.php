<?php

// If the other plugin is active, we deactivate.
if ( defined( GF_APPROVALS_VERSION ) && is_plugin_active( 'gravityformsapprovals/approvals.php' ) ) {
	deactivate_plugins( 'gravityformsapprovals/approvals.php' );
}

define( 'GF_APPROVALS_VERSION', '1.2.1' );

add_action( 'gform_loaded', array( 'GF_Approvals_Bootstrap', 'load' ), 5 );

class GF_Approvals_Bootstrap {

	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gf-approvals.php' );
		GFAddOn::register( 'GF_Approvals' );
	}

}
