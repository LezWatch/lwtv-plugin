<?php
/*
 * Debugging Tools for weird content
 *
 * @since 2.4
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Debug {

	/**
	 * Find Queers
	 *
	 * Find all characters who are mismatched with their queer settings
	 * and the actor who plays them
	 */
	public static function find_queerchars() {

		// Get all the characters
		$the_loop = LWTV_Loops::post_type_query( 'post_type_characters' );

		if ( $the_loop->have_posts() ) {
			while ( $the_loop->have_posts() ) {
				$the_loop->the_post();
				$post = get_post();

				// Get the actors...
				$character_actors = get_post_meta( $post->ID, 'lezchars_actor', true );

				if ( ! has_term( 'cartoon', 'lez_cliches' ) && ( ! $character_actors || empty( $character_actors ) ) ) {
					// If there are no actors and it's not a cartoon we have a different problem...
					$items[] = array(
						'url'     => get_permalink(),
						'id'      => get_the_id(),
						'problem' => 'No actors listed.',
					);
				} else {

					// Get the defaults
					$flagged_queer = ( has_term( 'queer-irl', 'lez_cliches' ) ) ? true : false;
					$actor_queer   = false;

					if ( ! is_array( $character_actors ) ) {
						$character_actors = array( get_post_meta( $post->ID, 'lezchars_actor', true ) );
					}

					// If ANY actor is flagged as queer, we're queer.
					foreach ( $character_actors as $actor ) {
						$actor_queer = ( 'yes' === LWTV_Loops::is_actor_queer( $actor ) || $actor_queer ) ? true : false;
					}

					if ( $actor_queer && ! $flagged_queer ) {
						$items[] = array(
							'url'     => get_permalink(),
							'id'      => get_the_id(),
							'problem' => 'Missing Queer IRL tag',
						);
					}

					if ( ! $actor_queer && $flagged_queer ) {
						$items[] = array(
							'url'     => get_permalink(),
							'id'      => get_the_id(),
							'problem' => 'No actor is queer',
						);
					}
				}
			}
			wp_reset_query();
		}

		return $items;

	}

	/**
	 * Find Actors with problems.
	 */
	public static function find_actors_problems() {

		// Default
		$items = array();

		// Get all the actors
		$the_loop = LWTV_Loops::post_type_query( 'post_type_actors' );

		if ( $the_loop->have_posts() ) {
			while ( $the_loop->have_posts() ) {
				$the_loop->the_post();
				$post     = get_post();
				$actor_id = $post->ID;
				$problems = array();

				// Check Character Count
				$character_count = get_post_meta( $actor_id, 'lezactors_char_count', true );
				if ( ! $character_count || empty( $character_count ) ) {
					$problems[] = 'No characters listed.';
				}

				if ( get_post_meta( $actor_id, 'lezactors_death', true ) ) {
					if ( ! get_post_meta( $actor_id, 'lezactors_birth', true ) ) {
						$problems[] = 'Death date set without date of birth.';
					}
				}

				if ( ! empty( $problems ) ) {
					$items[] = array(
						'url'     => get_permalink(),
						'id'      => get_the_id(),
						'problem' => implode( ' ', $problems ),
					);
				}
			}
			wp_reset_query();
		}

		return $items;
	}

	/**
	 * Fix Actors
	 *
	 * Right now all it can do is fix actors who are listed as having 0 characters
	 */
	public static function fix_actors_problems( $actors = 0 ) {

		$items = 0;

		if ( ! is_array( $actors ) ) {
			$actors = array();
			// Get all the actors
			$the_loop = LWTV_Loops::post_type_query( 'post_type_actors' );

			if ( $the_loop->have_posts() ) {
				while ( $the_loop->have_posts() ) {
					$the_loop->the_post();
					$post     = get_post();
					$actor_id = $post->ID;
					$problems = array();

					// Get the characters ...
					$character_count = get_post_meta( $actor_id, 'lezactors_char_count', true );

					// If there are no characters listed, let's try to fix
					if ( ! $character_count || empty( $character_count ) ) {
						$problems[] = 'character count';
						$actors[]   = array(
							'url'     => get_permalink(),
							'id'      => get_the_id(),
							'problem' => implode( ' ', $problems ),
						);
					}
				}
				wp_reset_query();
			}
		}

		// For everyone in the list...
		foreach ( $actors as $actor ) {
			LWTV_Actors_Calculate::do_the_math( $actor['id'] );
			$items++;
		}

		return $items;
	}

	/**
	 * Find Characters with Problems
	 */
	public static function find_characters_problems() {
		// Default
		$items = array();

		// Get all the shows
		$the_loop = LWTV_Loops::post_type_query( 'post_type_characters' );

		if ( $the_loop->have_posts() ) {
			while ( $the_loop->have_posts() ) {
				$the_loop->the_post();
				$post     = get_post();
				$char_id  = $post->ID;
				$problems = array();

				$cliche = get_the_terms( $char_id, 'lez_cliches' );
				if ( ! $cliche || is_wp_error( $cliche ) ) {
					$problems[] = 'No clichés.';
				}

				$last_death = get_post_meta( $char_id, 'lezchars_last_death', true );
				if ( has_term( 'dead', 'lez_cliches' ) && ! $last_death ) {
					$problems[] = 'Dead but missing date.';
				}

				$shows = get_post_meta( $char_id, 'lezchars_show_group', true );
				if ( ! $shows ) {
					$problems[] = 'No shows listed.';
				} else {
					foreach ( $shows as $each_show ) {
						if ( ! is_array( $each_show['appears'] ) ) {
							$problems[] = 'No years on air set for ' . get_the_title( $each_show['show'] ) . '.';
						}
						if ( ! isset( $each_show['type'] ) || '' === $each_show['type'] ) {
							$problems[] = 'No role set for' . get_the_title( $each_show['show'] ) . '.';
						}
						if ( ! isset( $each_show['show'] ) || '' === $each_show['show'] ) {
							$problems[] = 'No showname set.';
						}
					}
				}

				// If they're cartoons, they can have no actor.
				$actors = get_post_meta( $char_id, 'lezchars_actor', true );
				if ( ! $actors && ! has_term( 'cartoon', 'lez_cliches' ) ) {
					$problems[] = 'No actors listed.';
				}

				// If we have problems, list them:
				if ( ! empty( $problems ) ) {
					$items[] = array(
						'url'     => get_permalink(),
						'id'      => get_the_id(),
						'problem' => implode( ' ', $problems ),
					);
				}
			}
			wp_reset_query();
		}

		return $items;
	}

	/**
	 * Find Shows with Problems
	 */
	public static function find_shows_problems() {
		// Default
		$items = array();

		// Get all the shows
		$the_loop = LWTV_Loops::post_type_query( 'post_type_shows' );

		if ( $the_loop->have_posts() ) {
			while ( $the_loop->have_posts() ) {
				$the_loop->the_post();
				$post     = get_post();
				$show_id  = $post->ID;
				$problems = array();

				// Get what we check for:
				$character_count = get_post_meta( $show_id, 'lezshows_char_count', true );
				if ( ( ! $character_count || empty( $character_count ) ) && get_post_meta( $show_id, 'lezshows_screentime_rating', true ) ) {
					$problems[] = 'No characters listed.';
				}

				if ( ! get_post_meta( $show_id, 'lezshows_worthit_rating', true ) ) {
					$problems[] = 'No worthit thumb.';
				}

				if ( ! get_post_meta( $show_id, 'lezshows_worthit_details', true ) ) {
					$problems[] = 'No worthit details.';
				}

				if ( ! is_numeric( get_post_meta( $show_id, 'lezshows_realness_rating', true ) ) ) {
					$problems[] = 'No realness rating.';
				}

				if ( ! is_numeric( get_post_meta( $show_id, 'lezshows_quality_rating', true ) ) ) {
					$problems[] = 'No quality rating.';
				}

				if ( ! is_numeric( get_post_meta( $show_id, 'lezshows_screentime_rating', true ) ) ) {
					$problems[] = 'No screentime rating.';
				}

				$stations = get_the_terms( $show_id, 'lez_stations' );
				if ( ! $stations || is_wp_error( $stations ) ) {
					$problems[] = 'No stations.';
				}

				$nations = get_the_terms( $show_id, 'lez_country' );
				if ( ! $nations || is_wp_error( $nations ) ) {
					$problems[] = 'No country.';
				}

				$formats = get_the_terms( $show_id, 'lez_formats' );
				if ( ! $formats || is_wp_error( $formats ) ) {
					$problems[] = 'No format.';
				}

				if ( ! get_post_meta( $show_id, 'lezshows_airdates', true ) ) {
					$problems[] = 'No airdates.';
				}

				$genres = get_the_terms( $show_id, 'lez_genres' );
				if ( ! $genres || is_wp_error( $genres ) ) {
					$problems[] = 'No genres.';
				}

				$tropes = get_the_terms( $show_id, 'lez_tropes' );
				if ( ! $tropes || is_wp_error( $tropes ) ) {
					$problems[] = 'No tropes.';
				}

				// If we have problems, list them:
				if ( ! empty( $problems ) ) {
					$items[] = array(
						'url'     => get_permalink(),
						'id'      => get_the_id(),
						'problem' => implode( ' ', $problems ),
					);
				}
			}
			wp_reset_query();
		}

		return $items;
	}

}

new LWTV_Debug();
