<?php
/*
 * Find all problems with Queer data
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Debug_Queers {

	/**
	 * Find Queers
	 *
	 * Find all characters who are mismatched with their queer settings
	 * and the actor who plays them
	 */
	public function find_queerchars() {

		// Empty to start
		$items = array();

		// Get all the characters
		$the_loop = ( new LWTV_Loops() )->post_type_query( 'post_type_characters' );

		if ( $the_loop->have_posts() ) {
			$characters = wp_list_pluck( $the_loop->posts, 'ID' );
			wp_reset_query();
		}

		foreach ( $characters as $character ) {
			$problems = array();

			// Get the actors...
			$character_actors = get_post_meta( $character, 'lezchars_actor', true );

			if ( ! empty( $character_actors ) && is_array( $character_actors ) ) {
				// Get the defaults
				$flagged_queer = ( has_term( 'queer-irl', 'lez_cliches', $character ) ) ? true : false;
				$actor_queer   = false;

				// If ANY actor is flagged as queer, we're queer.
				foreach ( $character_actors as $actor ) {
					$actor_queer = ( 'yes' === ( new LWTV_Loops() )->is_actor_queer( $actor ) ) ? true : false;

					// If queer, we're done!
					if ( $actor_queer ) {
						break;
					}
				}

				if ( $actor_queer && ! $flagged_queer ) {
					$problems[] = 'Missing Queer IRL tag';
				}

				if ( ! $actor_queer && $flagged_queer ) {
					$problems[] = 'No actor is queer';
				}
			} else {
				$problems[] = 'No actors listed for this character';
			}

			if ( ! empty( $problems ) ) {
				$items[] = array(
					'url'     => get_permalink( $character ),
					'id'      => $character,
					'problem' => implode( '</br>', $problems ),
				);
			}
		}

		// Save Transient
		set_transient( 'lwtv_debug_queercheck', $items, WEEK_IN_SECONDS );

		// Update Options
		$option               = get_option( 'lwtv_debugger_status' );
		$option['queercheck'] = array(
			'name'  => 'Queer Checker',
			'count' => count( $items ),
			'last'  => time(),
		);
		$option['timestamp']  = time();
		update_option( 'lwtv_debugger_status', $option );

		return $items;

	}

}

new LWTV_Debug_Queers();
