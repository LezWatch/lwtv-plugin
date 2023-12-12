<?php

namespace LWTV\Theme;

class Actor_Characters {
	/**
	 * Generate Actor Data
	 *
	 * @access public
	 *
	 * @param  string $actor_id
	 * @param  string $format
	 *
	 * @return mixed
	 */
	public function make( $actor_id, $format ) {

		// Early Bail
		$valid_data = array( 'all', 'dead' );
		if ( ! in_array( $format, $valid_data, true ) ) {
			return;
		}

		$array  = array( $actor_id, $format );
		$output = self::$format( ...$array );

		return $output;
	}

	/**
	 * Generate list of characters
	 *
	 * @param string  $actor  ID Actor ID
	 * @param string  $format Type of Output
	 *
	 * @return array  All the characters by ID.
	 */
	public function all( $actor_id, $format ) {
		$format = $format;
		// Get array of characters (by ID).
		$character_array = get_post_meta( $actor_id, 'lezactors_char_list', true );

		// If the character list is empty, we must build it.
		if ( empty( $character_array ) ) {
			// Loop to get the list of characters
			$charactersloop = lwtv_plugin()->queery_post_meta( 'post_type_characters', 'lezchars_actor', $actor_id, 'LIKE' );
			wp_reset_query();

			if ( ! is_object( $charactersloop ) || ! $charactersloop->have_posts() ) {
				return;
			}

			$character_array = wp_list_pluck( $charactersloop->posts, 'ID' );

			if ( ! is_array( $character_array ) ) {
				$character_array = array( $character_array );
			}
			$character_array = array_unique( $character_array );
			update_post_meta( $actor_id, 'lezactors_char_list', $character_array );
		}

		// @TODO: There needs to be a way to invalidate this and re-run without a re-save.

		if ( is_array( $character_array ) ) {
			$characters = array();
			// Rebuild the character array in format:
			foreach ( $character_array as $char_id ) {
				$actors_array = get_post_meta( $char_id, 'lezchars_actor', true );
				if ( 'publish' === get_post_status( $char_id ) && isset( $actors_array ) && ! empty( $actors_array ) ) {
					foreach ( $actors_array as $char_actor ) {
						if ( (int) $char_actor === (int) $actor_id ) {
							$characters[ $char_id ] = array(
								'id'      => $char_id,
								'title'   => get_the_title( $char_id ),
								'url'     => get_the_permalink( $char_id ),
								'content' => get_the_content( $char_id ),
								'shows'   => get_post_meta( $char_id, 'lezchars_show_group', true ),
							);
						}
					}
				}
			}
			$character_array = $characters;
		}

		return $character_array;
	}

	/**
	 * Generate list of dead characters
	 *
	 * @param string  $actor  ID Actor ID
	 * @param string  $format Type of Output
	 *
	 * @return array  All the characters by ID.
	 */
	public function dead( $actor_id, $format ) {
		$format = $format;
		$dead   = array();

		// Get array of characters (by ID)
		$character_array = get_post_meta( $actor_id, 'lezactors_char_list', true );

		// If the character list is empty, we must build it
		if ( empty( $character_array ) ) {
			// Loop to get the list of characters
			$charactersloop = lwtv_plugin()->queery_post_meta( 'post_type_characters', 'lezchars_actor', $actor_id, 'LIKE' );
			wp_reset_query();

			if ( ! is_object( $charactersloop ) || ! $charactersloop->have_posts() ) {
				return;
			}

			$character_array = wp_list_pluck( $charactersloop->posts, 'ID' );
			$character_array = ( is_array( $character_array ) ) ? array_unique( $character_array ) : array_unique( array( $character_array ) );
			update_post_meta( $actor_id, 'lezactors_char_list', $character_array );
		}

		// @TODO: There needs to be a way to invalidate this and re-run without a re-save.

		if ( is_array( $character_array ) ) {
			foreach ( $character_array as $char_id ) {
				$actors = get_post_meta( $char_id, 'lezchars_actor', true );
				if ( isset( $actors ) && ! empty( $actors ) ) {
					foreach ( $actors as $actor ) {
						// We have to check because due to so many characters, we have some actor mis-matches.
						if ( ( (int) $actor === (int) $actor_id ) && has_term( 'dead', 'lez_cliches', $char_id ) ) {
							$dead[ $char_id ] = array(
								'id'    => $char_id,
								'title' => get_the_title( $char_id ),
								'url'   => get_the_permalink( $char_id ),
							);
						}
					}
				}
			}
		}
		return $dead;
	}
}