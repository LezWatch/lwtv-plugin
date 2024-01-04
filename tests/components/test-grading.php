<?php
/**
 * Class LWTV_Tests_Grading
 *
 * Functionality tests for Grading
 *
 * @package Lwtv_Plugin
 */

namespace LWTV\Tests;

use LWTV\_Components\Grading;
use LWTV\Plugin;

/**
 * Grading Tests.
 */
class Grading_Test extends \WP_UnitTestCase {

	/**
	 * Test class instance.
	 */
	private $instance;
	private $grading_post_id;

	/**
	 * Set up.
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		$this->instance = new Grading();

		$grading_score_meta = array(
			'lezshows_the_score'  => 100,
			'lezshows_3rd_scores' => array(
				'tvmaze' => array(
					'url'   => 'https://tvmaze.com',
					'score' => 99,
				),
				'tmdb' => array(
					'url'   => 'https://tmdb.com',
					'score' => 98,
				),
			),
		);

		// Make a post to use.
		$grading_post = array(
			'post_title'   => 'Fake Title',
			'post_content' => 'This is a post about a TV show and we will pretend things',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'post_type_shows',
			'meta_input'   => $grading_score_meta,
		);

		$this->grading_post_id = wp_insert_post( $grading_post );

	}

	/**
	 * Test the scores are set properly
	 */
	public function test_get_grade_lwtv() {
		$scores = lwtv_plugin()->get_all_scores( $this->grading_post_id );

		// Scores must be an array.
		$this->assertIsArray( $scores );

		// Test that the scores are the right numbers.
		$this->assertEquals( 100, $scores['lwtv']['score'] );
		$this->assertEquals( 99, $scores['tvmaze']['score'] );
		$this->assertEquals( 98, $scores['tmdb']['score'] );
	}
}
