<?php

namespace LWTV\Statistics\Build;

class Actor_Char_Role {

	/**
	 * Stats for character roles per actor.
	 *
	 * @param string $type   Post Type
	 * @param string $the_id Post ID
	 *
	 * @return array
	 */
	public function make( $type, $the_id ) {
		$transient = 'actor_char_role_' . $the_id;
		$array     = lwtv_plugin()->get_transient( $transient );

		if ( false === $array || empty( $array ) ) {
			// Force Array empty.
			$array      = array();
			$base_array = array(
				'regular'   => 0,
				'recurring' => 0,
				'guest'     => 0,
			);

			// Get array of characters (by ID)
			$char_array = get_post_meta( $the_id, 'lezactors_char_list', true );

			// If the character list is empty, we must build it
			if ( empty( $char_array ) ) {
				// Loop to get the list of characters
				$characters = lwtv_plugin()->queery_post_meta( 'post_type_characters', 'lezchars_actor', $the_id, 'LIKE' );

				if ( $characters->have_posts() ) {
					$char_array = wp_list_pluck( $characters->posts, 'ID' );
					wp_reset_query();
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
							if ( $char_actor == $the_id ) { // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
								$shows = get_post_meta( $char_id, 'lezchars_show_group', true );
								foreach ( $shows as $show ) {
									++$base_array[ $show['type'] ];
								}
							}
						}
					}
				}
			}

			foreach ( $base_array as $type => $count ) {
				$array[ $type ] = array(
					'count' => $count,
					'name'  => $type,
					'url'   => '',
				);
			}

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}