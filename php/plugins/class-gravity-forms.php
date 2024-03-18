<?php
/*
 * Gravity Forms for WordPress hooks
 * @package lwtv-plugin
 */

namespace LWTV\Plugins;

use LWTV\Plugins\Gravity_Forms\Stop_Spammers;
use LWTV\Plugins\Gravity_Forms\GF_Approvals;

class Gravity_Forms {

	public function __construct() {
		// https://docs.gravityforms.com/gform_disable_view_counter/#1-disable-for-all-forms
		add_filter( 'gform_disable_view_counter', '__return_true' );

		// Disable recaptcha on front page
		add_action( 'wp_print_scripts', array( $this, 'dequeue_scripts' ), 100 );

		new Stop_Spammers();

		// Call addon code.
		if ( method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			new GF_Approvals();
		}
	}

	/**
	 * Dequeue scripts for recaptcha UNLESS we're on a page with a form.
	 *
	 * @return void
	 */
	public function dequeue_scripts() {
		if ( is_front_page() || is_home() || is_singular( 'post' ) || is_archive() ) {
			wp_dequeue_script( 'gforms_recaptcha_recaptcha' );
			wp_dequeue_script( 'gforms_recaptcha_recaptcha_strings' );
		}
	}
}
