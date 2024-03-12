<?php

namespace LWTV\Theme;

use LWTV\CPTs\Characters;
use LWTV\CPTs\Shows;

class Show_Characters {
	/**
	 * Generate character lists
	 *
	 * @access public
	 *
	 * @param  string $show_id
	 * @param  string $format
	 *
	 * @return mixed
	 */
	public function make( $post_id, $format, $role = '' ) {

		$get_shadow_tax = \Shadow_Taxonomy\Core\get_the_posts( $post_id, Characters::SHADOW_TAXONOMY, Characters::SLUG );

		if ( $get_shadow_tax ) {
			$characters = $this->get_characters_from_shadow_tax( $get_shadow_tax, $format );
		} elseif ( taxonomy_exists( Characters::SHADOW_TAXONOMY ) ) {
			$characters = $this->get_characters_from_taxonomy( $post_id );
		} else {
			$characters = $this->get_characters_from_post_meta( $post_id, $format );
		}

		if ( ! empty( $role ) ) {
			$build_data = $this->build_character_data( $characters, $post_id, $role );
		} else {
			$build_data = $this->build_character_list( $characters, $post_id, $format );
		}

		return $build_data;
	}

	/**
	 * Get Characters For Show
	 *
	 * Get all the characters for a show, based on role type and output in
	 * a customized format for the show page.
	 *
	 * @param array  $characters Array of character IDs
	 * @param int    $show_id    ID of the show
	 * @param string $role       Role of the characters to look for
	 *
	 * @return array of characters with custom data to output
	 */
	public function build_character_data( $characters, $show_id, $role = 'regular' ): mixed {
		/**
		 * Funny things:
		 *   - The Sara Lance Complexity -- Because someone is on a lot of shows,
		 *                                  we have to make sure the IDs are right
		 *                                  and the show isn't a partial match.
		 *                                  Sara hasn't been on EVERY show yet.
		 *   - The Shane Clause          -- Thanks to Shane sleeping with everyone,
		 *                                  we had to limit this loop to 100 minimum
		 *   - The Clone Club Corollary  -- Sarah Manning took the place of every
		 *                                  single other character played by Tatiana
		 *                                  Maslany.
		 *   - The Vanishing Xenaphobia  -- When set to under 200, Xena doesn't show
		 *                                  on the Xena:WP show page
		 *   - Just a Phase Samantha     -- By the time we hit 6000 characters, the math
		 *                                  stopped working to show all the characters.
		 *                                  Now it's set to 1/10th the number of chars.
		 *   - The Shadow Tax            -- In order to prevent this from being an ongoing
		 *                                  issue, we use shadow taxonomies instead.
		 *
		 * Calculate the max number of characters to list, based on the
		 * previous count. Default/Minimum is the number of characters divided by 10
		 */

		// Valid Roles:
		$valid_roles = array( 'regular', 'recurring', 'guest' );

		// If this isn't a show page, or there are no valid roles, bail.
		if ( Shows::SLUG !== get_post_type( $show_id ) || ! in_array( $role, $valid_roles, true ) ) {
			return array();
		}

		// Empty array to display later
		$display = array();

		foreach ( $characters as $char_id ) {
			$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true );

			// If the character is in this show, AND a published character,
			// AND has this role ON THIS SHOW we will pass the following
			// data to the character template to determine what to display.
			if ( 'publish' === get_post_status( $char_id ) && isset( $shows_array ) && ! empty( $shows_array ) ) {
				foreach ( $shows_array as $char_show ) {

					// Remove the Array if it's there.
					if ( is_array( $char_show['show'] ) ) {
						$char_show['show'] = $char_show['show'][0];
					}

					// Because of show IDs having SIMILAR numbers, we need to be a little more flexible.
					// We PROBABLY don't need this anymore, but it's here just in case.

					// phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
					if ( $char_show['show'] == $show_id && $char_show['type'] === $role ) {
						$display[ $char_id ] = array(
							'id'        => $char_id,
							'title'     => get_the_title( $char_id ),
							'url'       => get_the_permalink( $char_id ),
							'content'   => get_the_content( $char_id ),
							'shows'     => $shows_array,
							'show_from' => $show_id,
							'role_from' => $role,
						);
					}
				}
			}
		}

		return $display;
	}

	/**
	 * Generate list of characters for shows
	 *
	 * @param array   $characters  Array of character IDs
	 * @param string  $show_id     ID of the show
	 * @param string  $output      Type of Output
	 *
	 * @return array  All the characters by ID.
	 */
	public function build_character_list( $characters, $show_id, $output ) {
		$return = array();

		$new_characters  = array();
		$dead_characters = array();
		$char_counts     = array(
			'total' => 0,
			'dead'  => 0,
			'none'  => 0,
			'quirl' => 0,
			'trans' => 0,
			'txirl' => 0,
		);

		if ( ! empty( $characters ) ) {

			foreach ( $characters as $char_id ) {
				// Get the list of shows.
				$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true );

				// If the character is in this show, AND a published character
				// we will pass the following data to the character template
				// to determine what to display.
				if (
					'' !== $shows_array &&
					! empty( $shows_array ) &&
					'publish' === get_post_status( $char_id )
				) {
					foreach ( $shows_array as $char_show ) {
						if ( is_array( $char_show['show'] ) ) {
							$char_show['show'] = $char_show['show'][0];
						}
						// phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
						if ( $char_show['show'] == $show_id ) {
							// Get a list of actors (we need this twice later)
							$actors_ids = get_post_meta( $char_id, 'lezchars_actor', true );
							if ( ! is_array( $actors_ids ) ) {
								$actors_ids = array( $actors_ids );
							}

							// The Queer Clone Calculations: The post query gets too many IDs
							// So we don't **REALLY** count then via this method unless the show
							// is there for the character.
							// Increase the count of characters
							++$char_counts['total'];
							$new_characters[] = $char_id;

							// Dead?
							if ( has_term( 'dead', 'lez_cliches', $char_id ) ) {
								++$char_counts['dead'];
								$dead_characters[] = $char_id;
							}
							// No cliches?
							if ( has_term( 'none', 'lez_cliches', $char_id ) ) {
								++$char_counts['none'];
							}
							// The Tambour Takedown: Checking Queer IRL
							// We don't award shows that have cast a cis/het actor in a queer
							// role. To solve this, we grab the actor listed as PRIMARY ACTOR
							// (i.e. the one listed first). If THEY are QIRL, the show gets points.
							if ( has_term( 'queer-irl', 'lez_cliches', $char_id ) ) {
								$top_actor = reset( $actors_ids );
								if ( lwtv_plugin()->is_actor_queer( $top_actor ) ) {
									++$char_counts['quirl'];
								}
							}

							// Is the character is not Cisgender ...
							$valid_trans_char = array( 'cisgender', 'intersex', 'unknown' );
							if ( ! has_term( $valid_trans_char, 'lez_gender', $char_id ) ) {
								++$char_counts['trans'];
							}

							// If an actor is transgender, we get an extra bonus.
							foreach ( $actors_ids as $actor ) {
								if ( lwtv_plugin()->is_actor_trans( $actor ) ) {
									++$char_counts['txirl'];
								}
							}
						}
					}
				}
			}
		}

		if ( empty( $new_characters ) ) {
			$new_characters = $characters;
		}

		switch ( $output ) {
			case 'dead':
				// Count of dead characters
				$return = $char_counts['dead'];
				break;
			case 'none':
				// count of characters with NO clichés
				$return = $char_counts['none'];
				break;
			case 'queer-irl':
				// count of characters who are queer IRL
				$return = $char_counts['quirl'];
				break;
			case 'trans':
				// Count of trans characters
				$return = $char_counts['trans'];
				break;
			case 'trans-irl':
				// count of characters who are trans IRL
				$return = $char_counts['txirl'];
				break;
			case 'query':
				// WP Array of all characters
				$return = $new_characters;
				break;
			case 'count':
				// Count of all characters on the show
				$return = count( $new_characters );
				break;
		}

		return $return;
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
	 * Get characters from the post meta
	 *
	 * @param int $show_id
	 *
	 * @return array IDs of characters.
	 */
	public function get_characters_from_post_meta( $show_id ) {
		// Get array of characters (by ID).
		$characters = get_post_meta( $show_id, 'lezshows_char_list', true );

		// If the character list is empty, we must build it
		if ( ! isset( $characters ) || empty( $characters ) ) {
			// Loop to get the list of characters
			$characters_loop = lwtv_plugin()->queery_post_meta( Characters::SLUG, 'lezchars_show_group', $show_id, 'LIKE' );

			if ( is_object( $characters_loop ) && $characters_loop->have_posts() ) {
				$characters = wp_list_pluck( $characters_loop->posts, 'ID' );
			}

			$characters = ( is_array( $characters ) ) ? array_unique( $characters ) : array( $characters );
		}

		return $characters;
	}


	/**
	 * Get characters from the taxonomy
	 *
	 * @param int $show_id
	 *
	 * @return array IDs of characters.
	 */
	public function get_characters_from_taxonomy( $show_id ) {
		$characters = array();
		$char_list  = wp_get_post_terms( $show_id, Characters::SHADOW_TAXONOMY, array( 'fields' => 'ids' ) );

		foreach ( $char_list as $char_id ) {
			$characters[] = get_term_meta( $char_id, 'shadow_shadow_tax_characters_post_id', true );
		}

		return $characters;
	}
}