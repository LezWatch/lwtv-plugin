<?php

namespace LWTV\Theme;

class Actor_Terms {
	/**
	 * Generate Actor Data based on terms
	 *
	 * @access public
	 *
	 * @param  string $actor_id
	 * @param  string $format
	 *
	 * @return mixed
	 */
	public function make( $actor_id, $format ) {

		$valid_terms = array( 'gender', 'sexuality' );
		if ( ! in_array( $format, $valid_terms, true ) ) {
			return;
		}

		$output    = '';
		$term_name = 'lez_actor_' . $format;
		$the_terms = get_the_terms( $actor_id, $term_name, true );
		if ( $the_terms && ! is_wp_error( $the_terms ) ) {
			foreach ( $the_terms as $a_term ) {
				$output .= '<a href="' . get_term_link( $a_term->slug, $term_name ) . '" rel="tag" title="' . $a_term->name . '">' . $a_term->name . '</a> ';
			}
		}

		return $output;
	}
}
