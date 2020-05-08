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
	 * Sanitize social media handles
	 * @param  string $string Username
	 * @param  string $for    Social Media Type
	 * @return string         sanitized username
	 */
	public static function sanitize_social( $string, $for ) {

		$clean = preg_replace( '/[^a-zA-Z_.0-9]/', '', $string );

		switch ( $for ) {
			case 'instagram':
				$trim = 30;
				break;
			case 'twiter':
				$trim = 15;
				break;
			default:
				$trim = 10;
		}

		$clean = substr( $clean, 0, $trim );

		return $clean;
	}

	/**
	 * Validate IMDB
	 * @param  string  $string IMDB ID
	 * @return boolean         true/false
	 */
	public static function validate_imdb( $string ) {

		$result = true;

		// IMDB looks like tt123456 or nm12356
		if ( substr( $string, 0, 2 ) === 'nm' || substr( $string, 0, 2 ) === 'tt' ) {
			if ( ! is_numeric( substr( $string, 2 ) ) ) {
				$result = false;
			}
		} else {
			$result = false;
		}

		return $result;
	}

	/**
	 * Find Queers
	 *
	 * Find all characters who are mismatched with their queer settings
	 * and the actor who plays them
	 */
	public static function find_queerchars() {

		// Empty to start
		$items = array();

		// Get all the characters
		$the_loop = LWTV_Loops::post_type_query( 'post_type_characters' );

		if ( $the_loop->have_posts() ) {
			while ( $the_loop->have_posts() ) {
				$the_loop->the_post();
				$post     = get_post();
				$problems = array();

				// Get the actors...
				$character_actors = get_post_meta( $post->ID, 'lezchars_actor', true );

				if ( ! empty( $character_actors ) ) {

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
						$problems[] = 'Missing Queer IRL tag';
					}

					if ( ! $actor_queer && $flagged_queer ) {
						$problems[] = 'No actor is queer';
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

		// Update Options
		$option = get_option( 'lwtv_debugger_status' );
		$option['queercheck'] = array(
			'name' => 'Queer Checker',
			'count' => count( $items ),
		);
		$option['timestamp']  = time();
		update_option( 'lwtv_debugger_status', $option );

		return $items;

	}

	/**
	 * Find Actors with problems.
	 */
	public static function find_actors_empty() {

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

				if ( ! has_post_thumbnail( $actor_id ) ) {
					$problems[] = 'No image found.';
				}

				if ( empty( get_the_content() ) ) {
					$problems[] = 'No biography found.';
				}

				// If we added any problems, loop and add.
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

		// Update Options
		$option = get_option( 'lwtv_debugger_status' );
		$option['actor_empty'] = array(
			'name' => 'Incomplete Actors',
			'count' => count( $items ),
		);
		$option['timestamp']  = time();
		update_option( 'lwtv_debugger_status', $option );

		return $items;

	}

	/**
	 * List Actors for Wikidata.
	 */
	public static function list_actors_wikidata() {

		// Default
		$items = array();

		// Get all the actors
		$the_loop = LWTV_Loops::post_type_query( 'post_type_actors' );

		if ( $the_loop->have_posts() ) {
			while ( $the_loop->have_posts() ) {
				$the_loop->the_post();
				$post = get_post();

				// Make item:
				$items[ $post->ID ] = get_the_title( $post->ID );
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

				// What we can check for
				$check = array(
					'chars' => get_post_meta( $actor_id, 'lezactors_char_count', true ),
					'birth' => get_post_meta( $actor_id, 'lezactors_birth', true ),
					'death' => get_post_meta( $actor_id, 'lezactors_death', true ),
					'wiki'  => get_post_meta( $actor_id, 'lezactors_wikipedia', true ),
					'imdb'  => get_post_meta( $actor_id, 'lezactors_imdb', true ),
					'insta' => get_post_meta( $actor_id, 'lezactors_instagram', true ),
					'twits' => get_post_meta( $actor_id, 'lezactors_twitter', true ),
					'home'  => get_post_meta( $actor_id, 'lezactors_website', true ),
				);

				if ( ! $check['chars'] || empty( $check['chars'] ) ) {
					$problems[] = 'No characters listed.';
				}

				if ( ! empty( $check['death'] ) ) {
					if ( empty( $check['birth'] ) ) {
						$problems[] = 'Death date set without date of birth.';
					}
				}

				// -Wikipedia links should point to Wikipedia: "https://[language].wikipedia.org/" (props Jamie)
				if ( ! empty( $check['wiki'] ) && strpos( $check['wiki'], 'wikipedia.org/' ) == false ) {
					$problems[] = 'Wikipedia URL does not point to Wikipedia.';
				}

				// -IMDb IDs should be valid for the space they're in, e.g. "nm" and digits for people (props Jamie)
				if ( ! empty( $check['imdb'] ) && self::validate_imdb( $check['imdb'] ) == false ) {
					$problems[] = 'IMDb ID is invalid (ex: nm12345).';
				}

				// -Instagram and Twitter usernames should follow whatever the actual restrictions on those are  (props Jamie)
				// -If Instagram or Twitter usernames are the same format as IMDb IDs, that's suspicious (props Jamie)
				if ( ! empty( $check['insta'] ) ) {
					// Limit - 30 symbols. Username must contains only letters, numbers, periods and underscores.
					if ( self::sanitize_social( $check['insta'], 'instagram' ) !== $check['insta'] && self::validate_imdb( $check['insta'] ) !== false ) {
						$problems[] = 'Instagram ID is invalid.';
					}
				}
				if ( ! empty( $check['twits'] ) ) {
					if ( self::sanitize_social( $check['twits'], 'twitter' ) !== $check['twits'] && self::validate_imdb( $check['twits'] ) !== false ) {
						$problems[] = 'Twitter ID is invalid.';
					}
				}

				// -"Website" links should *not* point to Wikipedia, since that would make them Wikipedia links (props Jamie)
				if ( ! empty( $check['home'] ) ) {
					if ( strpos( $check['home'], 'wikipedia.org/' ) !== false ) {
						$problems[] = 'Homepage points to Wikipedia.';
					}
				}

				// If we added any problems, loop and add.
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

		// Update Options
		$option = get_option( 'lwtv_debugger_status' );
		$option['actor_problems'] = array(
			'name' => 'Actors with Issues',
			'count' => count( $items ),
		);
		$option['timestamp']      = time();
		update_option( 'lwtv_debugger_status', $option );

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

				// What we can check for
				$check = array(
					'cliche' => get_the_terms( $char_id, 'lez_cliches' ),
					'death'  => get_post_meta( $char_id, 'lezchars_last_death', true ),
					'shows'  => get_post_meta( $char_id, 'lezchars_show_group', true ),
					'actors' => get_post_meta( $char_id, 'lezchars_actor', true ),
				);

				if ( ! $check['cliche'] || is_wp_error( $check['cliche'] ) ) {
					$problems[] = 'No clichés.';
				}

				if ( has_term( 'dead', 'lez_cliches' ) && empty( $check['death'] ) ) {
					$problems[] = 'Dead but missing date.';
				}

				if ( ! $check['shows'] ) {
					$problems[] = 'No shows listed.';
				} else {
					foreach ( $check['shows'] as $each_show ) {
						if ( ! is_array( $each_show['appears'] ) ) {
							$problems[] = 'No years on air set for ' . get_the_title( $each_show['show'] ) . '.';
						}
						if ( ! isset( $each_show['type'] ) || '' === $each_show['type'] ) {
							$problems[] = 'No role set for' . get_the_title( $each_show['show'] ) . '.';
						}
						if ( ! isset( $each_show['show'] ) || '' === $each_show['show'] ) {
							$problems[] = 'No show name set.';
						}
					}
				}

				// If they're cartoons, they can have no actor.
				if ( ! $check['actors'] && ! has_term( 'cartoon', 'lez_cliches' ) ) {
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

		// Update Options
		$option = get_option( 'lwtv_debugger_status' );
		$option['character_problems'] = array(
			'name' => 'Characters with Issues',
			'count' => count( $items ),
		);
		$option['timestamp']          = time();
		update_option( 'lwtv_debugger_status', $option );

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

				// What we can check for
				$check = array(
					'chars'      => get_post_meta( $show_id, 'lezshows_char_count', true ),
					'thumb'      => get_post_meta( $show_id, 'lezshows_worthit_rating', true ),
					'screentime' => get_post_meta( $show_id, 'lezshows_screentime_rating', true ),
					'details'    => get_post_meta( $show_id, 'lezshows_worthit_details', true ),
					'realness'   => get_post_meta( $show_id, 'lezshows_realness_rating', true ),
					'quality'    => get_post_meta( $show_id, 'lezshows_quality_rating', true ),
					'rating'     => get_post_meta( $show_id, 'lezshows_screentime_rating', true ),
					'airdates'   => get_post_meta( $show_id, 'lezshows_airdates', true ),
					'imdb'       => get_post_meta( $show_id, 'lezshows_imdb', true ),
					'stations'   => get_the_terms( $show_id, 'lez_stations' ),
					'nations'    => get_the_terms( $show_id, 'lez_country' ),
					'formats'    => get_the_terms( $show_id, 'lez_formats' ),
					'genres'     => get_the_terms( $show_id, 'lez_genres' ),
					'tropes'     => get_the_terms( $show_id, 'lez_tropes' ),
				);

				// If there's 0 screentime, it's okay there are no Characters
				if ( ( ! $check['chars'] || empty( $check['chars'] ) ) && $check['screentime'] > 1 ) {
					$problems[] = 'No characters listed.';
				}

				if ( ! array( $check['airdates'] ) ) {
					$problems[] = 'No airdates.';
				}

				if ( empty( $check['thumb'] ) ) {
					$problems[] = 'No worthit thumb.';
				}

				if ( empty( $check['details'] ) ) {
					$problems[] = 'No worthit details.';
				}

				if ( ! is_numeric( $check['realness'] ) ) {
					$problems[] = 'No realness rating.';
				}

				if ( ! is_numeric( $check['quality'] ) ) {
					$problems[] = 'No quality rating.';
				}

				if ( ! is_numeric( $check['screentime'] ) ) {
					$problems[] = 'No screentime rating.';
				}

				if ( ! empty( $check['imdb'] ) && self::validate_imdb( $check['imdb'] ) == false ) {
					$problems[] = 'IMDb ID is invalid (ex: tt12345).';
				}

				if ( ! $check['stations'] || is_wp_error( $check['stations'] ) ) {
					$problems[] = 'No stations.';
				}

				if ( ! $check['nations'] || is_wp_error( $check['nations'] ) ) {
					$problems[] = 'No country.';
				}

				if ( ! $check['formats'] || is_wp_error( $check['formats'] ) ) {
					$problems[] = 'No format.';
				}

				if ( ! $check['genres'] || is_wp_error( $check['genres'] ) ) {
					$problems[] = 'No genres.';
				}

				if ( ! $check['tropes'] || is_wp_error( $check['tropes'] ) ) {
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

		// Update Options
		$option = get_option( 'lwtv_debugger_status' );
		$option['show_problems'] = array(
			'name' => 'Shows with Issues',
			'count' => count( $items ),
		);
		$option['timestamp']     = time();
		update_option( 'lwtv_debugger_status', $option );

		return $items;
	}

}

new LWTV_Debug();
