<?php
/**
 * Library: Grading
 * Description: Display for LWTV Show Scores
 */

namespace LWTV\_Components;

use LWTV\Grading\Display;
use LWTV\Grading\LWTV;
use LWTV\Grading\TMDB;
use LWTV\Grading\TVMaze;

class Grading implements Component, Templater {

	/*
	 * Init
	 */
	public function init(): void {
		// Void
	}

	/**
	 * Template Tags
	 *
	 * @return array
	 */
	public function get_template_tags(): array {
		return array(
			'get_grade_color'       => array( $this, 'color' ),
			'display_scores'        => array( $this, 'display' ),
			'get_all_scores'        => array( $this, 'all_scores' ),
			'update_grading_scores' => array( $this, 'update_scores' ),
		);
	}

	/**
	 * Collect array of all scores
	 *
	 * @param  int   $show_id - ID of the show being graded
	 * @return array $scores - an array of show scores by source
	 */
	public function all_scores( $show_id ) {

		// Build Array
		$scores = array(
			'lwtv'   => ( new LWTV() )->get_all_data( $show_id ),
			'tmdb'   => ( new TMDB() )->get_all_data( $show_id ),
			'tvmaze' => ( new TVMaze() )->get_all_data( $show_id ),
		);

		return $scores;
	}

	/**
	 * Determine color of display, based on score
	 * Currently both are light, but that's subject to edits.
	 *
	 * @param  int    $score - the score being calculated from (default 0)
	 * @return string $color - the color based on the score
	 */
	public function color( $score = 0 ) {

		// force numbers all around
		$score = ( 'TBD' === strtoupper( $score ) || '0.00' === $score ) ? 0 : (int) $score - 1;

		// Invert because I mathed the colors backwards.
		$number = 100 - $score;

		if ( $number < 50 ) {
			// green to yellow
			$r = floor( 255 * ( $number / 50 ) );
			$g = 200;
		} else {
			// yellow to red
			$r = 255;
			$g = floor( 200 * ( ( 50 - $number % 50 ) / 50 ) );
		}
		$b = 0;

		$return = $r . ',' . $g . ',' . $b;

		return $return;
	}

	/**
	 * Display Scores
	 *
	 * @param  array $scores
	 * @return void
	 */
	public function display( $scores ) {
		return ( new Display() )->make( $scores );
	}

	/**
	 * Update all the scores.
	 *
	 * @param  int   $show_id - ID of the show being graded
	 * @return n/a   Saves to DB.
	 */
	public function update_scores( $show_id ) {
		$scores = array(
			'tmdb'   => ( new TMDB() )->update_scores( $show_id ),
			'tvmaze' => ( new TVMaze() )->update_scores( $show_id ),
		);

		update_post_meta( $show_id, 'lezshows_3rd_scores', $scores );
	}
}
