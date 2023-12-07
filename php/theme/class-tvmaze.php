<?php
/**
 * TV Maze data that isn't grading.
 */

namespace LWTV\Theme;

class TVMaze {

	/**
	 * TVMaze Episodes
	 *
	 * Calls TVMaze via the JSON code to collect the info for the next episode.
	 *
	 * @param int $show_id The Post ID of the show.
	 */
	public function episodes( int $show_id ): void {
		$tvmaze_episode = lwtv_plugin()->get_whats_on_show( $show_id );
		if ( isset( $tvmaze_episode['next'] ) && 'TBD' !== $tvmaze_episode['next'] ) {
			?>
			<li class="list-group-item network upcoming_ep">
				<strong>Next Episode:</strong> <?php echo esc_html( $tvmaze_episode['next'] ); ?>

				<?php
				// If there's a valid summary, add a button to show it
				if ( isset( $tvmaze_episode['next_summary'] ) && 'TBD' !== $tvmaze_episode['next_summary'] ) {
					// get TV Maze URLs
					$tvmaze = ( isset( $tvmaze_episode['tvmaze'] ) ) ? $tvmaze_episode['tvmaze'] : 'https://tvmaze.com/';
					?>
					<span class="badge text-bg-primary" type="button"  data-bs-toggle="collapse" data-bs-target="#episodeSummary" aria-expanded="false" aria-controls="episodeSummary">Read More</span>
					</li>

					<div class="collapse" id="episodeSummary">
						<div class="card">
							<div class="card-body">
								<p><?php echo wp_kses_post( $tvmaze_episode['next_summary'] ); ?>
								<br /><small><a href="<?php echo esc_url( $tvmaze ); ?>" target="_new">Powered by TVMaze</a></small></p>
							</div>
						</div>
					</div>
					<?php
				} else {
					echo '</li>';
				}
		}
	}
}
