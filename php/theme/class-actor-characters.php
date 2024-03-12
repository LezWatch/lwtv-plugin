<?php

namespace LWTV\Theme;

use LWTV\CPTs\Characters;
use function Shadow_Taxonomy\Core\get_the_posts;

/**
 * Generate character data for actors.
 */
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

		// if there is a character shadow tax, we need to get the characters from there.
		$get_shadow_tax = get_the_posts( $actor_id, Characters::SHADOW_TAXONOMY, Characters::SLUG );

		if ( $get_shadow_tax ) {
			$characters = $this->get_characters_from_shadow_tax( $get_shadow_tax );
		} elseif ( taxonomy_exists( Characters::SHADOW_TAXONOMY ) ) {
			$characters = $this->get_characters_from_taxonomy( $actor_id );
		} else {
			$characters = $this->get_characters_from_post_meta( $actor_id );
		}

		$build_data = $this->build_character_info( $characters, $actor_id );

		return $build_data;
	}

	/**
	 * Get characters from the shadow taxonomy
	 *
	 * @param array $shadow_array
	 *
	 * @return array IDs of characters.
	 */
	public function get_characters_from_shadow_tax( $shadow_array ) {
		$characters = array();

		foreach ( $shadow_array as $shadow ) {
			$characters[] = $shadow->ID;
		}

		return $characters;
	}

	/**
	 * Get characters from the taxonomy
	 *
	 * @param int $actor_id
	 *
	 * @return array IDs of characters.
	 */
	public function get_characters_from_taxonomy( $actor_id ) {
		$characters = array();
		$char_list  = wp_get_post_terms( $actor_id, Characters::SHADOW_TAXONOMY, array( 'fields' => 'ids' ) );

		foreach ( $char_list as $char_id ) {
			$characters[] = get_term_meta( $char_id, 'shadow_shadow_tax_characters_post_id', true );
		}

		return $characters;
	}

	/**
	 * Get characters from the post meta
	 *
	 * @param int $actor_id
	 *
	 * @return array IDs of characters.
	 */
	public function get_characters_from_post_meta( $actor_id ) {
		// Get array of characters (by ID).
		$character_array = get_post_meta( $actor_id, 'lezactors_char_list', true );

		// If the character list is empty, we must build it.
		if ( empty( $character_array ) ) {
			// Loop to get the list of characters
			$charactersloop = lwtv_plugin()->queery_post_meta( 'post_type_characters', 'lezchars_actor', $actor_id, 'LIKE' );

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

		return $character_array;
	}

	/**
	 * Build minimal character info
	 *
	 * Thanks to the L Word having over 60 characters (I don't know
	 * why I'm surprised by this...) having the full content loop was
	 * causing server overload. This way we build the smaller loop.
	 *
	 * @param array $character_array
	 * @param int   $actor_id
	 *
	 * @return array
	 */
	public function build_character_info( array $character_array, int $actor_id ) {
		// @TODO: There needs to be a way to invalidate this and re-run without a re-save.

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

		return $characters;
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
		$character_array = $this->all( $actor_id, 'all' );

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
