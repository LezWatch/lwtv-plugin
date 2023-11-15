<?php

class LWTV_Stats_Scores {

	/*
	 * Statistics Scores
	 *
	 * @return array
	 */
	public function build( $post_type ) {

		$transient = 'scores_' . $post_type;
		$array     = LWTV_Transients::get_transient( $transient );

		if ( false === $array ) {

			$the_queery = ( new LWTV_Loops() )->post_type_query( $post_type );
			$array      = array();

			if ( $the_queery->have_posts() ) {
				$scores_shows = wp_list_pluck( $the_queery->posts, 'ID' );
				wp_reset_query();
			}

			if ( is_array( $scores_shows ) ) {
				foreach ( $scores_shows as $show_id ) {
					$array[ $show_id ] = array(
						'id'    => $show_id,
						'count' => get_post_meta( $show_id, 'lezshows_the_score', true ),
						'url'   => get_the_permalink( $show_id ),
					);
				}
			}

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}

new LWTV_Stats_Scores();
