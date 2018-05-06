<?php
/*
 * Debugging Tools for weird content
 *
 * @since 2.4
 */

// if this file is called directly abort
if ( ! defined('WPINC' ) ) {
	die;
}

class LWTV_Debug {


	/**
	 * Find Queers
	 * 
	 * Find all characters who are mismatched with their queer settings 
	 * and the actor who plays them
	 */
	static function find_queerchars() {

		// Get all the characters
		$the_loop = LWTV_Loops::post_type_query( 'post_type_characters' );

		if ( $the_loop->have_posts() ) {
			while ( $the_loop->have_posts() ) {
				$the_loop->the_post();
				$post = get_post();

				// Get the actors...
				$character_actors = get_post_meta( $post->ID, 'lezchars_actor', true );

				if( !$character_actors || empty( $character_actors ) ) {
					// If there are no actors, we have a different problem...
					$items[] = array( 'url' => get_permalink(), 'id' => get_the_id(), 'problem' => 'No actors listed.' );
				} else {

					// Get the defaults
					$flagged_queer = ( has_term( 'queer-irl', 'lez_cliches' ) )? true : false;
					$actor_queer   = false;

					if ( !is_array ( $character_actors ) ) {
						$character_actors = array( get_post_meta( $post->ID, 'lezchars_actor', true ) );
					}

					// If ANY actor is flagged as queer, we're queer.
					foreach ( $character_actors as $actor ) {
						$actor_queer = ( LWTV_Loops::is_actor_queer( $actor ) == 'yes' || $actor_queer )? true : false;
					}
					
					if ( $actor_queer && !$flagged_queer ) {
						$items[] = array( 'url' => get_permalink(), 'id' => get_the_id(), 'problem' => 'Missing Queer IRL tag' );
					}

					if ( !$actor_queer && $flagged_queer ) {
						$items[] = array( 'url' => get_permalink(), 'id' => get_the_id(), 'problem' => 'No actor is queer' );
					}
				}
			}
			wp_reset_query();
		}

		return $items;

	}

}

new LWTV_Debug();