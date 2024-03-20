<?php

namespace LWTV\Theme;

use LWTV\CPTs\Characters;

class Character_Relationships {
	/**
	 * Generate Relationship data.
	 *
	 * @access public
	 *
	 * @param  string $char_id   - Character ID
	 * @return array  $all_chars - Array of Character IDs
	 */
	public function make( $char_id ) {
		// Get THIS char's shadow tax ID:
		$term_id = get_post_meta( $char_id, sanitize_key( 'shadow_' . Characters::SHADOW_TAXONOMY . '_term_id' ), true );

		// Get a list of all characters who have that shadow tax:
		$shadow_queery = lwtv_plugin()->queery_taxonomy( Characters::SLUG, Characters::SHADOW_TAXONOMY, 'term_id', $term_id );
		$shadow_chars  = array();

		// Turn $shadow_queery object into array:
		if ( is_object( $shadow_queery ) ) {
			if ( $shadow_queery->have_posts() ) {
				while ( $shadow_queery->have_posts() ) {
					$shadow_queery->the_post();
					$shadow_chars[] = get_the_ID();
				}
			}
		}

		// Get the post_meta for lezchars_relationship_chart for THIS character:
		$relationships = get_post_meta( $char_id, 'lezchars_relationship_chart', true );

		// Combine lists (all chars who ref this char, and all chars this char refs):
		$all_chars = array_merge( $shadow_chars, $relationships );

		// Remove dupes.
		$all_chars = array_unique( $all_chars );

		// Output.
		return $all_chars;
	}
}
