<?php
/**
 * Name: Actor Calculations
 * Description: Calculate various data points for actors
 */

namespace LWTV\CPTs\Actors;

use LWTV\CPTs\Characters;

class Calculations {

	/*
	 * Count all characters for an actor.
	 *
	 * @param int $post_id The post ID of the actor.
	 */
	public function count( $post_id, $type = 'count' ) {

		$type_array        = array( 'count', 'none', 'dead' );
		$character_checked = array();

		// If this isn't an actor post or a valid request, return nothing
		if ( 'post_type_actors' !== get_post_type( $post_id ) || ! in_array( $type, $type_array, true ) ) {
			return;
		}

		// Get array of characters (by ID)
		$characters = lwtv_plugin()->get_actor_characters( $post_id );

		// Process character counts:
		$queercount = 0;
		$deadcount  = 0;

		if ( is_array( $characters ) ) {
			foreach ( $characters as $char_id => $char_details ) {

				// If the character isn't published, skip it.
				if ( get_post_status( $char_id ) !== 'publish' ) {
					continue;
				}

				$actors_array = get_post_meta( $char_id, 'lezchars_actor', true );
				$is_dead      = has_term( 'dead', 'lez_cliches', $char_id );

				if ( '' !== $actors_array && is_array( $actors_array ) && 'publish' === get_post_status( $char_id ) ) {
					foreach ( $actors_array as $char_actor ) {
						if ( (int) $char_actor === (int) $post_id ) {
							++$queercount;
							if ( $is_dead ) {
								++$deadcount;
							}
						}
					}
				}
			}
		}

		// Let us return Queers!
		switch ( $type ) {
			case 'count':
				$output = $queercount;
				break;
			case 'dead':
				$output = $deadcount;
				break;
		}

		return $output;
	}

	/**
	 * do_the_math function.
	 *
	 * This will update the following metakeys on save:
	 *  - lezactors_char_count      Number of characters
	 *  - lezactors_dead_count      Number of dead characters
	 *  - lezactors_queer           Are they queer? True or false
	 *
	 * @access public
	 * @param  int  $post_id
	 * @return void
	 */
	public function do_the_math( $post_id ): void {

		// Calculate meta:
		$all_chars  = self::count( $post_id, 'count' );
		$dead_chars = self::count( $post_id, 'dead' );
		$is_queer   = lwtv_plugin()->is_actor_queer( $post_id );

		// Update Meta:
		update_post_meta( $post_id, 'lezactors_char_count', $all_chars );
		update_post_meta( $post_id, 'lezactors_dead_count', $dead_chars );
		update_post_meta( $post_id, 'lezactors_queer', $is_queer );
	}
}
