<?php

namespace LWTV\Statistics\Build;

class Actor_Char_Dead {

	/**
	 * Stats for dead character per actor.
	 *
	 * @param string $type   Post Type
	 * @param string $the_id Post ID
	 *
	 * @return array
	 */
	public function make( $type, $the_id ) {
		// Default
		$array     = array();
		$post_type = $type;

		$transient = 'actor_char_dead_' . $the_id;
		$array     = lwtv_plugin()->get_transient( $transient );

		if ( false === $array || empty( $array ) ) {
			$base_array = array(
				'alive' => array(
					'count' => 0,
					'name'  => 'alive',
					'url'   => '',
				),
				'dead'  => array(
					'count' => 0,
					'name'  => 'dead',
					'url'   => '',
				),
			);

			// Get array of characters (by ID)
			$char_array = get_post_meta( $the_id, 'lezactors_char_list', true );

			// If the character list is empty, we must build it
			if ( empty( $char_array ) ) {
				// Loop to get the list of characters
				$characters = lwtv_plugin()->queery_post_meta( 'post_type_characters', 'lezchars_actor', $the_id, 'LIKE' );

				if ( is_object( $characters ) && $characters->have_posts() ) {
					$char_array = wp_list_pluck( $characters->posts, 'ID' );
				}

				$char_array = array_unique( $char_array );

				foreach ( $char_array as $char_id ) {
					$actors = get_post_meta( $char_id, 'lezchars_actor', true );
					if ( 'publish' === get_post_status( $char_id ) && isset( $actors ) && ! empty( $actors ) ) {
						foreach ( $actors as $actor ) {
							// We have to check because due to so many characters, we have some actor mis-matches.
							if ( $actor == $the_id ) {  // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
								$character_checked[] = $the_id;
							}
						}
					}
				}

				$char_array = $character_checked;
				update_post_meta( $the_id, 'lezactors_char_list', $char_array );
			}

			if ( is_array( $char_array ) ) {
				foreach ( $char_array as $char_id ) {
					$actors_array = get_post_meta( $char_id, 'lezchars_actor', true );
					if ( 'publish' === get_post_status( $char_id ) && isset( $actors_array ) && ! empty( $actors_array ) ) {
						foreach ( $actors_array as $char_actor ) {
							if ( $char_actor == $the_id ) {  // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
								if ( has_term( 'dead', 'lez_cliches', $char_id ) ) {
									++$base_array['dead']['count'];
								} else {
									++$base_array['alive']['count'];
								}
							}
						}
					}
				}
			}

			$array = $base_array;

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}
