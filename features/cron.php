<?php
/**
 * Name: Cron
 * Description: Some custom jobs we schedule for cron things.
 * Version: 1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Cron
 *
 * All Some custom jobs we schedule for cron things.
 *
 * @since 1.0
 */

class LWTV_Cron {

	/**
	 * hourly_urls
	 *
	 * @var mixed
	 * @access public
	 */
	public $hourly_urls;

	/**
	 * daily_urls
	 *
	 * @var mixed
	 * @access public
	 */
	public $daily_urls;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// URLs we need to prime the pump on a little more often than normal
		$this->hourly_urls = array(
			'/statistics/',
			'/statistics/characters/',
			'/statistics/shows/',
			'/statistics/death/',
			'/statistics/trends/',
			'/statistics/nations/',
			'/statistics/stations/',
			'/actors/',
			'/characters/',
			'/shows/',
			'/show/the-l-word/',
			'/',
		);

		// URLs we need to refresh daily
		$this->daily_urls = array(
			'wp-json/lwtv/v1/last-death/',
			'wp-json/lwtv/v1/stats/',
			'wp-json/lwtv/v1/of-the-day/',
			'wp-json/lwtv/v1/of-the-day/character/',
			'wp-json/lwtv/v1/of-the-day/show/',
			'wp-json/lwtv/v1/of-the-day/death/',
		);

		add_action( 'lwtv_cache_event_hourly', array( $this, 'varnish_cache_hourly' ) );
		add_action( 'lwtv_cache_event_daily', array( $this, 'varnish_cache_daily' ) );

		if ( ! wp_next_scheduled( 'lwtv_cache_event_hourly' ) ) {
			wp_schedule_event( time(), 'hourly', 'lwtv_cache_event_hourly' );
		}

		if ( ! wp_next_scheduled( 'lwtv_cache_event_daily' ) ) {
			wp_schedule_event( time(), 'daily', 'lwtv_cache_event_daily' );
		}
	}

	/**
	 * Hourly Cache checks.
	 *
	 * @access public
	 * @return void
	 */
	public function varnish_cache_hourly() {
		foreach ( $this->hourly_urls as $url ) {
			wp_remote_get( home_url( $url ) );
		}
	}

	/**
	 * Daily Cache checks.
	 *
	 * @access public
	 * @return void
	 */
	public function varnish_cache_daily() {
		foreach ( $this->daily_urls as $url ) {
			wp_remote_get( home_url( $url ) );
		}
	}

}

new LWTV_Cron();
