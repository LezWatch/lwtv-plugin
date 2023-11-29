<?php

final class LWTV_Grading_Scores_Factory {

	private const SOURCE_MAPPINGS = array(
		'lwtv'   => 'LWTV_Grading_LWTV_Build',
		'tmdb'   => 'LWTV_Grading_TMDB_Build',
		'tvmaze' => 'LWTV_Grading_TVMaze_Build',
	);

	public function make( string $source, int $show_id ) {
		if ( array_key_exists( $source, self::SOURCE_MAPPINGS ) ) {
			$score_class = self::SOURCE_MAPPINGS[ $source ];
			return new $score_class( $show_id );
		}
	}
}
