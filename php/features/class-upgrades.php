<?php
/**
 * Upgrade Control
 */

namespace LWTV\Features;

// Prevent auto upgrades if we're on the dev site.
if ( defined( 'LWTV_DEV_SITE' ) && LWTV_DEV_SITE ) {
	return;
}

class Upgrades {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Run finders and actions on inits.
	 *
	 * @return void
	 */
	public function init(): void {
		// Allow Updates
		add_filter( 'auto_update_core', '__return_true' );

		// Don't update core themes and plugins (we don't need them)
		define( 'CORE_UPGRADE_SKIP_NEW_BUNDLED', true );

		// Force updates even if Git is there.
		add_filter( 'automatic_updates_is_vcs_checkout', '__return_false', 1 );

		// Force auto plugin updates:
		add_filter( 'auto_update_plugin', '__return_true' );

		// Force auto theme updates
		add_filter( 'auto_update_theme', '__return_true' );

		// Suspend or force emails (false == no email ; true == yes email)
		add_filter( 'auto_core_update_send_email', '__return_false', 1 );
		add_filter( 'automatic_updates_send_debug_email', '__return_true', 1 );
	}
}
