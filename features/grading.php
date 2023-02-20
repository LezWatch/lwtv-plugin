<?php
/*
Library: Grading
Description: Display for LWTV Show Scores
Version: 1.0
Author: Mika Epstein
*/

class LWTV_Grading {

	/**
	 * Collect array of all scores
	 *
	 * @param  int   $show_id - ID of the show being graded
	 * @return array $scores - an array of show scores by source
	 */
	public function all_scores( $show_id ) {

		// Local scores
		$lwtv = ( get_post_meta( $show_id, 'lezshows_the_score', true ) && is_numeric( (int) get_post_meta( $show_id, 'lezshows_the_score', true ) ) ) ? round( min( (int) get_post_meta( $show_id, 'lezshows_the_score', true ), 100 ) ) : 'TBD';

		// External scores
		$external = get_post_meta( $show_id, 'lezshows_3rd_scores', true );
		$tmdb     = ( isset( $external['tmdb']['score'] ) ) ? round( $external['tmdb']['score'] ) : 'TBD';
		$tvmaze   = ( isset( $external['tvmaze']['score'] ) ) ? round( $external['tvmaze']['score'] ) : 'TBD';
		$tomato   = ( isset( $external['tomato']['score'] ) ) ? round( $external['tomato']['score'] ) : 'TBD';
		$fresh    = ( $tomato >= 60 ) ? 'fresh' : 'splat';

		// Build Array
		$scores = array(
			'lwtv'   => array(
				'image' => plugins_url( '/assets/images/scores/lwtv.png', dirname( __FILE__ ) ),
				'name'  => 'LezWatchTV',
				'score' => $lwtv,
				'color' => self::color( $lwtv ),
				'bg'    => '#d1548e',
				'url'   => '/about/scoring-queer-shows/',
			),
			'tomato' => array(
				'image' => plugins_url( '/assets/images/scores/tomato-' . $fresh . '.svg', dirname( __FILE__ ) ),
				'name'  => 'Rotten Tomatoes',
				'score' => $tomato,
				'color' => self::color( $tomato ),
				'bg'    => '#2a2c32',
				'url'   => 'https://www.rottentomatoes.com',
			),
			'tmdb'   => array(
				'image' => plugins_url( '/assets/images/scores/tmdb.svg', dirname( __FILE__ ) ),
				'name'  => 'The Movie Database',
				'score' => $tmdb,
				'color' => self::color( $tmdb ),
				'bg'    => '#0d253f',
				'url'   => 'https://themoviedb.com',
			),
			'tvmaze' => array(
				'image' => plugins_url( '/assets/images/scores/tvmaze.png', dirname( __FILE__ ) ),
				'name'  => 'TV Maze',
				'score' => $tvmaze,
				'color' => self::color( $tvmaze ),
				'bg'    => '#3c948b',
				'url'   => $external['tmdb']['score'],
			),
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
		$score = ( 'TBD' === strtoupper( $score ) ) ? 0 : (int) $score - 1;

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

}

new LWTV_Grading();
