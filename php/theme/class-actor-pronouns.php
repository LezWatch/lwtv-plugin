<?php

namespace LWTV\Theme;

class Actor_Pronouns {
	/**
	 * Generate actor pronouns.
	 *
	 * @access public
	 *
	 * @param  int    $the_id
	 * @return string
	 */
	public function make( $actor_id ): string {
		$pronouns          = array(
			'singular' => array(),
			'plural'   => array(),
		);
		$singular_pronouns = array( 'Any', 'He', 'She', 'Her', 'Hir', 'They', 'Xe', 'Ze' );

		$pronoun_terms = get_the_terms( $actor_id, 'lez_actor_pronouns', true );
		if ( $pronoun_terms && ! is_wp_error( $pronoun_terms ) ) {

			foreach ( $pronoun_terms as $pronoun_term ) {
				if ( in_array( $pronoun_term->name, $singular_pronouns, true ) ) {
					$pronouns['singular'][] = $pronoun_term->name;
				} else {
					$pronouns['plural'][] = $pronoun_term->name;
				}
			}
		}

		$build_pronouns  = ( ! empty( $pronouns['singular'] ) ) ? implode( '/', $pronouns['singular'] ) : '';
		$build_pronouns .= ( ! empty( $pronouns['plural'] ) ) ? implode( '/', $pronouns['plural'] ) : '';

		return $build_pronouns;
	}
}
