<?php
/*
 * Environment Settings
 *
 */

namespace LWTV\Features;

class Environment {

	// Default environment.
	public $default_env_type;

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( function_exists( 'wp_get_environment_type' ) && ! empty( wp_get_environment_type() ) ) {
			$this->default_env_type = 'uilabs-' . wp_get_environment_type();
		}

		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'admin_body_class', array( &$this, 'admin_body_class' ) );
	}

	/**
	 * Init
	 */
	public function init() {
		wp_register_style( 'ui-labs-identity', plugins_url( 'assets/css/environment.css', dirname( __DIR__, 1 ) ), false, LWTV_PLUGIN_VERSION );
		wp_enqueue_style( 'ui-labs-identity' );
	}

	/**
	 * Identify Server UI Experiment
	 *
	 * Allows users to easily spot whether they are logged in to their development, staging, or live server.
	 */
	public function admin_body_class( $classes ) {
		if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
			$classes .= ' ' . $this->default_env_type . ' ';
		}
		return $classes;
	}
}
