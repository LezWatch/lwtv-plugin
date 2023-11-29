<?php

final class LWTV_Grading_LWTV_Build extends LWTV_Grading_Scores {

	/**
	 * Get all the data for LWTV
	 *
	 * @param  int $show_id
	 * @return array
	 */
	public function get_all_data( int $show_id ): array {
		$score = $this->get_score( $show_id );

		return array(
			'image' => plugins_url( '/assets/images/scores/lwtv.png', dirname( __DIR__, 1 ) ),
			'name'  => 'LezWatchTV',
			'score' => $score,
			'color' => ( new LWTV_Grading() )->color( $score ),
			'bg'    => '#d1548e',
			'url'   => $this->get_url( $show_id ),
		);
	}

	/**
	 * Get the LWTV score
	 *
	 * @param  int   $show_id
	 * @return float
	 */
	public function get_score( int $show_id ): float {
		return ( get_post_meta( $show_id, 'lezshows_the_score', true ) && is_numeric( (int) get_post_meta( $show_id, 'lezshows_the_score', true ) ) ) ? round( min( (int) get_post_meta( $show_id, 'lezshows_the_score', true ), 100 ) ) : '0.00';
	}

	/**
	 * Get the LWTV URL (never changes)
	 *
	 * @param  int    $show_id
	 * @return string
	 */
	public function get_url( int $show_id ): string {
		return site_url( '/about/scoring-queer-shows/' );
	}

	/**
	 * Update the LWTV scores
	 *
	 * @param  int  $show_id
	 * @return void
	 */
	public function update_scores( int $show_id ): void {
		( new LWTV_Shows_Calculate() )->do_the_math( $show_id );
	}
}
