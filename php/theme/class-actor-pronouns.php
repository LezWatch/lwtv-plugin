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
		$pronouns         = array(
			'subject' => array(),
			'object'  => array(),
		);
		$subject_pronouns = array( 'Any', 'He', 'Per', 'She', 'They', 'Ve', 'Xe', 'Ze', 'Zie' );

		$pronoun_terms = get_the_terms( $actor_id, 'lez_actor_pronouns', true );
		if ( $pronoun_terms && ! is_wp_error( $pronoun_terms ) ) {

			foreach ( $pronoun_terms as $pronoun_term ) {
				if ( in_array( $pronoun_term->name, $subject_pronouns, true ) ) {
					$pronouns['subject'][] = $pronoun_term->name;
				} else {
					$pronouns['object'][] = $pronoun_term->name;
				}
			}
		}

		$build_pronouns  = ( ! empty( $pronouns['subject'] ) ) ? implode( '/', $pronouns['subject'] ) : '';
		$build_pronouns .= ( ! empty( $pronouns['object'] ) ) ? '/' . implode( '/', $pronouns['object'] ) : '';

		return $build_pronouns;
	}
}
