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
	 * URLs we prime
	 */
	public $hourly_urls;
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

		add_filter( 'cron_schedules', array( $this, 'custom_cron_schedule' ) );
		add_action( 'init', array( $this, 'missed_schedule' ) );

		add_action( 'lwtv_cache_event_hourly', array( $this, 'varnish_cache_hourly' ) );
		if ( ! wp_next_scheduled( 'lwtv_cache_event_hourly' ) ) {
			wp_schedule_event( time(), 'hourly', 'lwtv_cache_event_hourly' );
		}

		add_action( 'lwtv_cache_event_daily', array( $this, 'varnish_cache_daily' ) );
		if ( ! wp_next_scheduled( 'lwtv_cache_event_daily' ) ) {
			wp_schedule_event( time(), 'daily', 'lwtv_cache_event_daily' );
		}

		/*
		// Currently we're going to try transients instead...
		add_action( 'lwtv_posts_missed_schedule', array( $this, 'missed_schedule' ) );
		if ( ! wp_next_scheduled( 'lwtv_posts_missed_schedule' ) ) {
			wp_schedule_event( time(), 'every-15-minutes', 'lwtv_posts_missed_schedule' );
		}
		*/
	}

	/**
	 * Custom cron schedule of every 15 minutes
	 */
	public function custom_cron_schedule( $schedules ) {
		$schedules['every-15-minutes'] = array(
			'interval' => 15 * MINUTE_IN_SECONDS,
			'display'  => 'Every 15 minutes',
		);
		return $schedules;
	}

	/**
	 * Missed schedule fixes. Hopefully.
	 */
	public function missed_schedule() {

		global $wpdb;

		$missed_transient = get_transient( 'lwtv_missed_schedule' );
		if ( false === ( $missed_transient ) ) {
			// If there's no transient, set it for 15 minutes
			$checktime = ( HOUR_IN_SECONDS / 4 );
			set_transient( 'lwtv_missed_schedule', 'check_posts', $checktime );
		} else {
			// If there is a transient and it hasn't expired, don't run this at all
			return;
		}

		$queery = <<<SQL
SELECT ID FROM {$wpdb->posts} WHERE ( ( post_date > 0 && post_date <= %s ) ) AND post_status = 'future' LIMIT 0,10
SQL;

		$sql = $wpdb->prepare( $queery, current_time( 'mysql', 0 ) );
		$ids = $wpdb->get_col( $sql );

		// There are no posts missed schedule so don't run anything.
		if ( ! count( $ids ) ) {
			return;
		}

		foreach ( $ids as $the_id ) {
			if ( ! $the_id ) {
				continue;
			}
			wp_publish_post( $the_id );
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
