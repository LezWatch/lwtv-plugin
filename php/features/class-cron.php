<?php
/**
 * Name: Cron
 * Description: Some custom jobs we schedule for cron things.
 * Version: 1.5
 */

namespace LWTV\Features;

/**
 * All Some custom jobs we schedule for cron things.
 *
 * @since 1.0
 */

class Cron {

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
		// Trigger Crons.
		add_filter( 'cron_schedules', array( $this, 'custom_cron_schedule' ) );
		add_action( 'init', array( $this, 'missed_schedule' ) );

		// Update TVMaze
		add_action( 'lwtv_tv_maze_hourly', array( $this, 'tv_maze_cron' ) );
		if ( ! wp_next_scheduled( 'lwtv_tv_maze_hourly' ) ) {
			wp_schedule_event( time(), 'daily', 'lwtv_tv_maze_hourly' );
		}

		// Check the tools
		add_action( 'lwtv_tools', array( $this, 'tools_check' ) );
		if ( ! wp_next_scheduled( 'lwtv_tools' ) ) {
			wp_schedule_event( strtotime( '03:01:00' ), 'daily', 'lwtv_tools' );
		}

		// Update Of The Day: Shows
		add_action( 'lwtv_show_of_the_day', array( $this, 'show_of_the_day' ) );
		if ( ! wp_next_scheduled( 'lwtv_show_of_the_day' ) ) {
			wp_schedule_event( strtotime( '09:01:00' ), 'daily', 'lwtv_show_of_the_day' );
		}

		// Update Of The Day: Chars
		add_action( 'lwtv_char_of_the_day', array( $this, 'char_of_the_day' ) );
		if ( ! wp_next_scheduled( 'lwtv_char_of_the_day' ) ) {
			wp_schedule_event( strtotime( '13:01:00' ), 'daily', 'lwtv_char_of_the_day' );
		}
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

		$missed_transient = lwtv_plugin()->get_transient( 'lwtv_missed_schedule' );
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

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$sql = $wpdb->prepare( $queery, current_time( 'mysql', 0 ) );
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
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
	 * Update Lists
	 *
	 * Update lists of shows and actors as transients to speed up queeries
	 * and make them cacheable.
	 *
	 * @access public
	 * @return void
	 */
	public function lists_daily() {
		$count_shows = lwtv_plugin()->get_transient( 'lwtv_count_shows' );
		if ( false === $count_shows ) {
			$count_shows = wp_count_posts( 'post_type_shows' )->publish;
			set_transient( 'lwtv_count_shows', $count_shows, 24 * HOUR_IN_SECONDS );
		}

		$count_actors = lwtv_plugin()->get_transient( 'lwtv_count_actors' );
		if ( false === $count_actors ) {
			$count_actors = wp_count_posts( 'post_type_actors' )->publish;
			set_transient( 'lwtv_count_actors', $count_actors, 24 * HOUR_IN_SECONDS );
		}
	}

	/**
	 * TV Maze Cron Job
	 *
	 * Saves the ICS data to a file so we're not overloading the API.
	 *
	 * @access public
	 * @return void
	 */
	public function tv_maze_cron() {
		lwtv_plugin()->download_tvmaze();
	}

	/**
	 * Do a deep dive and check for problems.
	 *
	 * Run a different check every day to lower load.
	 *
	 * @access public
	 * @return void
	 */
	public function tools_check() {
		// Run the indexer
		\FWP()->indexer->index();

		// Run a different check each day.
		switch ( gmdate( 'D' ) ) {
			case 'Mon':
				lwtv_plugin()->find_actors_problems();
				break;
			case 'Tue':
				lwtv_plugin()->find_actors_no_imdb();
				break;
			case 'Wed':
				lwtv_plugin()->find_duplicates();
				break;
			case 'Thu':
				lwtv_plugin()->find_queer_chars();
				break;
			case 'Fri':
				lwtv_plugin()->find_characters_problems();
				break;
			case 'Sat':
				lwtv_plugin()->find_shows_problems();
				break;
			case 'Sun':
				lwtv_plugin()->find_shows_no_imdb();
				break;
		}
	}

	/**
	 * Set the show of the day.
	 */
	public function show_of_the_day() {
		lwtv_plugin()->set_of_the_day( 'show' );
	}

	/**
	 * Set the character of the day.
	 */
	public function char_of_the_day() {
		lwtv_plugin()->set_of_the_day( 'character' );
	}
}
