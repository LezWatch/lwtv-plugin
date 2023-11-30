<?php

final class LWTV_Grading_TVMaze_Build extends LWTV_Grading_Scores {

	/**
	 * Get All TVMaze data
	 *
	 * @param  int $show_id
	 * @return array
	 */
	public function get_all_data( int $show_id ): array {
		return array(
			'image' => plugins_url( '/assets/images/scores/tvmaze.png', dirname( __DIR__, 1 ) ),
			'name'  => 'TV Maze',
			'score' => $this->get_score( $show_id ),
			'color' => ( new LWTV_Grading() )->color( $this->get_score( $show_id ) ),
			'bg'    => '#3c948b',
			'url'   => $this->get_url( $show_id ),
		);
	}

	/**
	 * Get TVMaze Score
	 *
	 * @param  int   $show_id
	 * @return float
	 */
	public function get_score( int $show_id ): float {
		// External scores
		$external = get_post_meta( $show_id, 'lezshows_3rd_scores', true );

		return ( isset( $external['tvmaze']['score'] ) ) ? round( (int) $external['tvmaze']['score'] ) : '0.00';
	}

	/**
	 * Update TVMaze URL
	 *
	 * @param  int    $show_id
	 * @return string
	 */
	public function get_url( int $show_id ): string {
		$external = get_post_meta( $show_id, 'lezshows_3rd_scores', true );

		return ( isset( $external['tvmaze']['url'] ) ) ? $external['tvmaze']['url'] : 'https://tvmaze.com';
	}

	/**
	 * Update TVMaze Scores
	 *
	 * @param  int   $show_id
	 * @return array
	 */
	public function update_scores( int $show_id ): array {
		$score   = 'TBD';
		$url     = $this->get_url( $show_id );
		$imdb_id = get_post_meta( $show_id, 'lezshows_imdb', true );
		$recheck = false;

		// Only call their service once a day.
		$transient = LWTV_Features_Transients::get_transient( 'lwtv_3rd_scores_tvmaze_' . $show_id );
		if ( false === $transient ) {
			$recheck = true;
		} else {
			$score   = $transient;
			$recheck = ( 'TBD' !== $score ) ? false : true;
		}

		if ( $imdb_id && $recheck ) {
			$response = wp_remote_get( 'http://api.tvmaze.com/lookup/shows?imdb=' . $imdb_id );

			// Check the response:
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$body = json_decode( $response['body'], true ); // use the content

				// TV Maze returns a null body sometimes.
				if ( ! is_null( $body ) ) {
					$url   = $body['url'];
					$score = ( isset( $body['rating']['average'] ) && ! empty( $body['rating']['average'] ) ) ? round( $body['rating']['average'] * 10 ) : 'TBD';
				}
			}

			// Set transient and don't re-check until tomorrow.
			set_transient( 'lwtv_3rd_scores_tvmaze_' . $show_id, $score, 24 * HOUR_IN_SECONDS );
		}

		return array(
			'score' => $score,
			'url'   => $url,
		);
	}
}
