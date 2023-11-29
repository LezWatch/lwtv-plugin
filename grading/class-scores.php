<?php


abstract class LWTV_Grading_Scores {

	public function __construct(
		protected int $show_id,
	) {
	}

	/**
	 * Get all the data by Show ID
	 *
	 * @param  int $show_id
	 * @return array
	 */
	abstract public function get_all_data( int $show_id ): array;

	/**
	 * Get the score for a show
	 *
	 * @param  int $show_id
	 * @return float
	 */
	abstract public function get_score( int $show_id ): float;

	/**
	 * Get the URL
	 *
	 * @param  int    $show_id
	 * @return string
	 */
	abstract public function get_url( int $show_id ): string;

	/**
	 * Update the scores
	 *
	 * @param  int  $show_id
	 * @return void
	 */
	abstract public function update_scores( int $show_id );
}
