<?php
/*
Library: Grading
Description: Display for LWTV Show Scores
Version: 1.0
Author: Mika Epstein
*/

class LWTV_Features_Grading {

	/**
	 * Collect array of all scores
	 *
	 * @param  int   $show_id - ID of the show being graded
	 * @return array $scores - an array of show scores by source
	 */
	public function all_scores( $show_id ) {

		// Local scores
		$lwtv = array(
			'score' => ( get_post_meta( $show_id, 'lezshows_the_score', true ) && is_numeric( (int) get_post_meta( $show_id, 'lezshows_the_score', true ) ) ) ? round( min( (int) get_post_meta( $show_id, 'lezshows_the_score', true ), 100 ) ) : 'TBD',
			'url'   => site_url( '/about/scoring-queer-shows/' ),
		);

		// External scores
		$external = get_post_meta( $show_id, 'lezshows_3rd_scores', true );
		$tmdb     = array(
			'score' => ( isset( $external['tmdb']['score'] ) ) ? round( (int) $external['tmdb']['score'] ) : 'TBD',
			'url'   => ( isset( $external['tmdb']['url'] ) ) ? $external['tmdb']['url'] : 'https://themoviedb.org',
		);
		$tvmaze   = array(
			'score' => ( isset( $external['tvmaze']['score'] ) ) ? round( (int) $external['tvmaze']['score'] ) : 'TBD',
			'url'   => ( isset( $external['tvmaze']['url'] ) ) ? $external['tvmaze']['url'] : 'https://tvmaze.com',
		);

		// Build Array
		$scores = array(
			'lwtv'   => array(
				'image' => plugins_url( '/assets/images/scores/lwtv.png', __DIR__ ),
				'name'  => 'LezWatchTV',
				'score' => $lwtv['score'],
				'color' => self::color( $lwtv['score'] ),
				'bg'    => '#d1548e',
				'url'   => $lwtv['url'],
			),
			'tmdb'   => array(
				'image' => plugins_url( '/assets/images/scores/tmdb.svg', __DIR__ ),
				'name'  => 'The Movie Database',
				'score' => $tmdb['score'],
				'color' => self::color( $tmdb['score'] ),
				'bg'    => '#0d253f',
				'url'   => $tmdb['url'],
			),
			'tvmaze' => array(
				'image' => plugins_url( '/assets/images/scores/tvmaze.png', __DIR__ ),
				'name'  => 'TV Maze',
				'score' => $tvmaze['score'],
				'color' => self::color( $tvmaze['score'] ),
				'bg'    => '#3c948b',
				'url'   => $tvmaze['url'],
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

		echo '<center>';
		foreach ( $scores as $score ) {
			if ( isset( $score['score'] ) && 0 !== (int) $score['score'] && 'TBD' !== strtoupper( $score['score'] ) ) {
				?>
				<a href="<?php echo esc_url( $score['url'] ); ?>" target="new">
				<button
					aria-label="The <?php echo esc_html( $score['name'] ); ?> score is <?php echo esc_html( $score['score'] ); ?> out of 100"
					type="button"
					class="btn btn-light" style="background-color: <?php echo esc_html( $score['bg'] ); ?>; min-width: 100px;margin-bottom:3px;">
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
						<?php
							$x_size = ( isset( $score['alt_s'] ) ) ? '7' : '9';
						?>
						<text x="<?php echo (int) $x_size; ?>" y="23" class="percentage">
							<?php
							echo esc_html( isset( $score['alt_s'] ) ? $score['alt_s'] : $score['score'] );
							?>
						</text><
					</svg>
				</button>
				</a>
				<?php
			}
		}

		// If you're logged in and can edit posts, you can refresh the scores.
		if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
			$post_id = get_the_ID();
			if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'lwtv-update-scores' ) ) {
				( new LWTV_Shows_Calculate() )->do_the_math( $post_id );
				sleep( 5 );
				wp_safe_redirect( get_the_permalink( $post_id ) );
				exit;
			}

			?>
			<form id="update_scores" name="update_scores" method="post">
				<center><p><br /><input type="submit" value="Refresh Scores" tabindex="6" id="submit" name="submit" /></p></center>
				<?php wp_nonce_field( 'lwtv-update-scores' ); ?>
			</form>
			<?php
			echo '</center>';
		}
	}

	/**
	 * Update all the scores.
	 *
	 * @param  int   $show_id - ID of the show being graded
	 * @return n/a   Saves to DB.
	 */
	public function update_scores( $post_id ) {
		self::api_tmdb( $post_id );
		self::api_tvmaze( $post_id );
	}

	/**
	 * Get the score from TMDB
	 *
	 * @param  int   $show_id - ID of the show being graded
	 * @return n/a   Saves to DB.
	*/
	public function api_tmdb( $show_id ) {

		$score   = 'TBD';
		$url     = 'https://themoviedb.org/';
		$imdb_id = get_post_meta( $show_id, 'lezshows_imdb', true );
		$current = get_post_meta( $show_id, 'lezshows_3rd_scores', true );
		$recheck = false;

		// Only call their service once a day.
		$transient = LWTV_Features_Transients::get_transient( 'lwtv_3rd_scores_tmdb_' . $show_id );
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
					$url  .= ( isset( $body['tv_results'][0]['id'] ) ) ? 'tv/' . $body['tv_results'][0]['id'] : '';
				}
			}

			// Set transient and don't re-check until tomorrow.
			set_transient( 'lwtv_3rd_scores_tmdb_' . $show_id, $score, 24 * HOUR_IN_SECONDS );
		}

		if ( ! is_array( $current ) ) {
			$current = array();
		}

		$current['tmdb'] = array(
			'score' => $score,
			'url'   => $url,
		);

		update_post_meta( $show_id, 'lezshows_3rd_scores', $current );
	}

	/**
	 * Get the score from TVMAZE
	 *
	 * @param  int   $show_id - ID of the show being graded
	 * @return n/a   Update meta.
	 */
	public function api_tvmaze( $show_id ) {
		$score   = 'TBD';
		$url     = 'https://tvmaze.com/';
		$imdb_id = get_post_meta( $show_id, 'lezshows_imdb', true );
		$current = get_post_meta( $show_id, 'lezshows_3rd_scores', true );
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

		if ( ! is_array( $current ) ) {
			$current = array();
		}

		$current['tvmaze'] = array(
			'score' => $score,
			'url'   => $url,
		);

		update_post_meta( $show_id, 'lezshows_3rd_scores', $current );
	}
}
