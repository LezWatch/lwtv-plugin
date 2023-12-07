<?php
/**
 * Display Content
 */

namespace LWTV\Grading;

class Display {

	/**
	 * Make the display
	 *
	 * @param  array $scores
	 * @return void
	 */
	public function make( $scores ) {
		// Early Check.
		if ( ! is_array( $scores ) ) {
			return;
		}

		echo '<center>';
		foreach ( $scores as $score ) {
			if ( null !== $score['score'] && 0 !== (int) $score['score'] && 'TBD' !== strtoupper( $score['score'] ) ) {
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
				lwtv_plugin()->calculate_show_data( $post_id );
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
}
