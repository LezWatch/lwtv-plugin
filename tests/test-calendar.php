<?php
/**
 * Class LWTV_Tests_Calendar
 *
 * Functionality tests for Ways to Watch
 *
 * @package Lwtv_Plugin
 */

namespace LWTV\Tests;

use LWTV\_Components\Calendar;
use LWTV\Plugin;

/**
 * Calendar Tests.
 */
class Calendar_Test extends \WP_UnitTestCase {

	/**
	 * Test downloading TV Maze as that it works.
	 */
	public function test_download_tvmaze() {
		define( 'TV_MAZE', 'https://lezwatchtv.com/wp-content/uploads/tvmaze.ics' );

		( new Calendar() )->download_tvmaze();

		$upload_dir = wp_upload_dir();
		$ics_file   = $upload_dir['basedir'] . '/tvmaze.ics';
		$file_time  = filemtime( $ics_file );

		$this->assertFileExists( $ics_file );
	}
}
