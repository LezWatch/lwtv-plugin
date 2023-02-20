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

	public function display( $scores ) {
		// Early Check.
		if ( ! is_array( $scores ) ) {
			return;
		}

		foreach ( $scores as $score ) {
			if ( isset( $score['score'] ) && 'TBD' !== strtoupper( $score['score'] ) ) {
				?>
				<a href="<?php echo esc_url( $score['url'] ); ?>" target="new">
				<button 
					aria-label="The <?php echo esc_html( $score['name'] ); ?> score is <?php echo esc_html( $score['score'] ); ?> out of 100" 
					type="button" 
					class="btn btn-light" style="background-color: <?php echo esc_html( $score['bg'] ); ?>; min-width: 100px;">
					<center>
						<img 
							alt="Powered By <?php echo esc_html( $score['name'] ); ?>"
							src="<?php echo esc_url( $score['image'] ); ?>" 
							style="max-height: 36px;padding-bottom: 4px;"
						/>
					</center>
					<svg 
						viewBox="0 0 36 36"
						aria-hidden="true"
					>
						<path
							d="M18 2.0845
							a 15.9155 15.9155 0 0 1 0 31.831
							a 15.9155 15.9155 0 0 1 0 -31.831"
							fill="#f3f3f3"
							stroke-alignment="inner"
							stroke="rgb( <?php echo esc_html( $score['color'] ); ?> )"
							stroke-width="2.8"
							stroke-dasharray="<?php echo esc_html( $score['score'] ); ?>, 100"
						/>
						<text x="10" y="23" class="percentage"><?php echo esc_html( $score['score'] ); ?></text><
					</svg>
				</button>
				</a>
				<?php
			}
		}
	}

}

new LWTV_Grading();
