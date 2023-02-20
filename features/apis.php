<?php
/*
Library: APIs
Description: Calling weird APIs we use.
Version: 1.0
Author: Mika Epstein
*/

class LWTV_External_APIs {

	/**
	 * TVMaze Episodes
	 *
	 * Calls TVMaze via the JSON code to collect the info for the next episode.
	 *
	 * @param int $show_id The Post ID of the show.
	 */
	public function tvmaze_episodes( $show_id ) {
		$tvmaze_episode = ( new LWTV_Whats_On_JSON() )->whats_on_show( $show_id );
		if ( isset( $tvmaze_episode['next'] ) && 'TBD' !== $tvmaze_episode['next'] ) {
			echo '<li class="list-group-item network upcoming_ep"><strong>Next Episode:</strong> ' . esc_html( $tvmaze_episode['next'] );

			// If there's a valid summary, add a button to show it
			if ( isset( $tvmaze_episode['next_summary'] ) && 'TBD' !== $tvmaze_episode['next_summary'] ) {
				// get TV Maze URLs
				$tvmaze   = ( isset( $tvmaze_episode['tvmaze'] ) ) ? $tvmaze_episode['tvmaze'] : 'https://tvmaze.com/';
				$collapse = 'data-toggle="collapse" href="#episodeSummary" role="button" aria-expanded="false" aria-controls="episodeSummary"';
				echo '<br /><button class="btn btn-primary btn-sm btn-block" type="button" data-toggle="collapse" data-target="#episodeSummary" aria-expanded="false" aria-controls="episodeSummary">Read More</button></li>';
				echo '<div class="collapse" id="episodeSummary"><div class="card card-body">' . esc_html( stripslashes( $tvmaze_episode['next_summary'] ) ) . '<br /><small><a href="' . esc_url( $tvmaze ) . '" target="_new">Powered by TVMaze</a></small></div></div>';
			} else {
				echo '</li>';
			}
		}
	}
}

new LWTV_External_APIs();
