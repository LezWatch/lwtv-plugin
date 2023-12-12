<?php
/*
 * Find all problems with Queer data
 */

namespace LWTV\Debugger;

class Queers {

	/**
	 * Find Queers
	 *
	 * Find all characters who are mismatched with their queer settings
	 * and the actor who plays them
	 */
	public function find_queerchars( $items = array() ) {

		// The array we will be checking.
		$characters = array();

		// Are we a full scan or a recheck?
		if ( ! empty( $items ) ) {
			// Check only the characters from items!
			foreach ( $items as $character_item ) {
				if ( get_post_status( $character_item['id'] ) !== 'draft' ) {
					// If it's NOT a draft, we'll recheck.
					$characters[] = $character_item['id'];
				}
			}
		} else {
			// Get all the characters
			$the_loop = lwtv_plugin()->queery_post_type( 'post_type_characters' );

			if ( is_object( $the_loop ) && $the_loop->have_posts() ) {
				$characters = wp_list_pluck( $the_loop->posts, 'ID' );
				wp_reset_query();
			}
		}

		// If somehow characters is totally empty...
		if ( empty( $characters ) ) {
			return false;
		}

		// Make sure we don't have dupes.
		$characters = array_unique( $characters );

		// reset items since we recheck off $characters.
		$items = array();

		// Reset items
		$items = array();

		// If this is WP-CLI, setup progress bar.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$progress_bar = \WP_CLI\Utils\make_progress_bar( sprintf( 'Starting queer checker. Found %d characters...', count( $characters ) ), count( $characters ) );
		}

		foreach ( $characters as $character ) {

			// If this is WP-CLI, tick progress bar.
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				$progress_bar->tick();
			}

			$problems = array();

			// Get the actors...
			$character_actors = get_post_meta( $character, 'lezchars_actor', true );

			if ( ! empty( $character_actors ) && is_array( $character_actors ) ) {
				// Get the defaults
				$flagged_queer = ( has_term( 'queer-irl', 'lez_cliches', $character ) ) ? true : false;
				$actor_queer   = false;

				// If ANY actor is flagged as queer, we're queer.
				foreach ( $character_actors as $actor ) {
					$actor_queer = lwtv_plugin()->is_actor_queer( $actor );

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

		// If this is WP-CLI, finish progress bar.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$progress_bar->finish();
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
