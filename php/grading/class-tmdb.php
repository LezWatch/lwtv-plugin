<?php
/**
 * Calculate grades for LWTV
 */

namespace LWTV\Grading;

class TMDB {

	/**
	 * Get All TMDB Data
	 *
	 * @param  int   $show_id
	 * @return array
	 */
	public function get_all_data( int $show_id ): array {
		return array(
			'image' => plugins_url( '/assets/images/scores/tmdb.png', dirname( __DIR__, 1 ) ),
			'name'  => 'The Movie Database',
			'score' => $this->get_score( $show_id ),
			'color' => lwtv_plugin()->get_grade_color( $this->get_score( $show_id ) ),
			'bg'    => '#0d253f',
			'url'   => $this->get_url( $show_id ),
		);
	}

	/**
	 * Get TMDB Score
	 *
	 * @param  int   $show_id
	 * @return float
	 */
	public function get_score( int $show_id ): float {
		$external = get_post_meta( $show_id, 'lezshows_3rd_scores', true );
		return ( isset( $external['tmdb']['score'] ) ) ? round( (int) $external['tmdb']['score'] ) : '0.00';
	}

	/**
	 * Update TMDB URL
	 *
	 * @param  int    $show_id
	 * @return string
	 */
	public function get_url( int $show_id ): string {
		$external = get_post_meta( $show_id, 'lezshows_3rd_scores', true );
		return ( isset( $external['tmdb']['url'] ) ) ? $external['tmdb']['url'] : 'https://themoviedb.org';
	}

	/**
	 * Update TMDB Scores
	 *
	 * @param  int  $show_id
	 * @return array
	 */
	public function update_scores( int $show_id ): array {
		$score   = 'TBD';
		$url     = $this->get_url( $show_id );
		$imdb_id = get_post_meta( $show_id, 'lezshows_imdb', true );
		$recheck = false;

		// Only call their service once a day.
		$transient = lwtv_plugin()->get_transient( 'lwtv_3rd_scores_tmdb_' . $show_id );
		if ( false === $transient ) {
			$recheck = true;
		} else {
			$score   = $transient;
			$recheck = ( 'TBD' !== $score ) ? false : true;
		}

		// Make sure the API is defined
		if ( defined( 'TMDB_API' ) && $imdb_id && $recheck ) {
			$response = wp_remote_get( 'https://api.themoviedb.org/3/find/' . $imdb_id . '?api_key=' . TMDB_API . '&external_source=imdb_id' );

			// Check the response:
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$body = json_decode( $response['body'], true ); // use the content

				// If there's a status message, it's an error:
				if ( ! isset( $body['status_message'] ) ) {
					$score = ( isset( $body['tv_results'][0]['vote_average'] ) ) ? round( $body['tv_results'][0]['vote_average'] * 10 ) : 'TBD';
					$url   = ( isset( $body['tv_results'][0]['id'] ) ) ? 'https://themoviedb.org/tv/' . $body['tv_results'][0]['id'] : '';
				}
			}

			// Set transient and don't re-check until tomorrow.
			set_transient( 'lwtv_3rd_scores_tmdb_' . $show_id, $score, 24 * HOUR_IN_SECONDS );
		}

		return array(
			'score' => $score,
			'url'   => $url,
		);
	}
}
