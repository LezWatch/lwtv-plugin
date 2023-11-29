<?php

class LWTV_Theme_Show_Stars {
	/**
	 * Show star
	 *
	 * If a show has a star, let's show it.
	 *
	 * @access public
	 *
	 * @param  string $show_id
	 * @return mixed  Star (or not)
	 */
	public function make( $show_id ) {

		$star_terms = get_the_terms( $show_id, 'lez_stars' );

		if ( get_post_meta( $show_id, 'lezshows_stars', true ) || ( ! empty( $star_terms ) && ! is_wp_error( $star_terms ) ) ) {
			$color = get_post_meta( $show_id, 'lezshows_stars', true );

			if ( ! empty( $star_terms ) && ! is_wp_error( $star_terms ) ) {
				$color_term = get_the_terms( $show_id, 'lez_stars' );
				$color      = $color_term[0]->slug;
			}

			$icon = ( new LWTV_Features() )->symbolicons( 'star.svg', 'fa-star' );
			$star = ' <span role="img" aria-label="' . ucfirst( $color ) . ' Star Show" data-bs-target="tooltip" title="' . ucfirst( $color ) . ' Star Show" class="show-star ' . $color . '">' . $icon . '</span>';

			return $star;
		} else {
			return;
		}
	}
}
