<?php
/**
 * Name: Cron
 * Description: Some custom jobs we schedule for cron things.
 * Version: 1.0
 */

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Cron
 *
 * @since 1.0
 */

class LWTV_Cron {

	public $urls;
	
	/**
	 * Constructor
	 */
	public function __construct() {

		// URLs we need to prime the pump on a little more often than normal
		$this->urls = array(
			'/statistics/',
			'/statistics/characters/',
			'/statistics/shows/',
			'/statistics/death/',
			'/statistics/trends/',
			'/characters/',
			'/shows/',
			'/show/the-l-word/',
			'/',
		);

		add_action( 'lwtv_cache_event', array( $this, 'varnish_cache' ) );

		if ( !wp_next_scheduled ( 'lwtv_cache_event' ) ) {
			wp_schedule_event( time(), 'hourly', 'lwtv_cache_event' );
		}
	}

	public function varnish_cache() {
		foreach ( $this->urls as $url ) {
			wp_remote_get( home_url( $url ) );
		}
	}

}

new LWTV_Cron();