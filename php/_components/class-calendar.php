<?php
/**
 * Calendar Builder
 *
 * Adds custom post type for TVMaze Show Names
 */

namespace LWTV\_Components;

use LWTV\Calendar\ICS_Parser;
use LWTV\Calendar\Names;

class Calendar implements Component, Templater {

	/**
	 * Constructor
	 */
	public function init() {
		new ICS_Parser();
		new Names();
	}

	/**
	 * Gets tags to expose as methods accessible through `lwtv_plugin()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function get_template_tags(): array {
		return array(
			'generate_ics_by_date'       => array( $this, 'generate_ics_by_date' ),
			'get_show_name_for_calendar' => array( $this, 'get_show_name_for_calendar' ),
			'download_tvmaze'            => array( $this, 'download_tvmaze' ),
			'get_tvmaze_ics'             => array( $this, 'get_tvmaze_ics' ),
		);
	}

	/**
	 * Generate what's on for a specific date
	 *
	 * @param  string $url  URL of calendar
	 * @param  string $when string of a day [today, tomorrow]
	 * @param  string $date date event happens [Y-m-d]
	 *
	 * @return array        array of all the shows on that day
	 */
	public function generate_ics_by_date( $url, $when, $date = false ): array {
		return ( new ICS_Parser() )->generate_by_date( $url, $when, $date );
	}

	/**
	 * Since TV Maze sometimes uses different names than we do, we have to make a related array that can handle two names.
	 *
	 * @param string $show_name — Display Name of the show
	 * @param string $source    — lwtv or tvmaze
	 *
	 * @return string — The display name
	 */
	public function get_show_name_for_calendar( $show_name, $source = 'lwtv' ): string {
		return ( new Names() )->make( $show_name, $source );
	}

	public function get_tvmaze_ics() {
		$upload_dir  = wp_upload_dir();
		$tvmaze_file = $upload_dir['basedir'] . '/tvmaze.ics';

		if ( ! file_exists( $tvmaze_file ) ) {
			return false;
		}

		return $tvmaze_file;
	}

	/**
	 * Download TV Maze
	 *
	 * Saves the ICS data to a file so we're not overloading the API.
	 *
	 * @param $ics_file  Location of ICS file (optional)
	 *
	 * @return void
	 */
	public function download_tvmaze( $ics_file = null ): void {
		$ics_file = ( is_null( $ics_file ) ) ? self::get_tvmaze_ics() : $ics_file;
		$response = wp_remote_get( TV_MAZE );
		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents( $ics_file, $response['body'] );
		}
	}
}
