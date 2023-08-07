<?php
/*
 * Find all problems with Actor pages.
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Debug_Actors {

		/**
	 * Find Actors with problems.
	 */
	public function find_actors_problems() {

		// Default
		$items = array();

		// Get all the actors
		$the_loop = ( new LWTV_Loops() )->post_type_query( 'post_type_actors' );

		if ( $the_loop->have_posts() ) {
			$actors = wp_list_pluck( $the_loop->posts, 'ID' );
			wp_reset_query();
		}

		foreach ( $actors as $actor_id ) {
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
				'home'  => get_post_meta( $actor_id, 'lezactors_homepage', true ),
				'dupe'  => get_post_field( 'post_name', $actor_id ),
			);

			// - Confirm there are characters listed.
			if ( ! $check['chars'] || empty( $check['chars'] ) ) {
				$problems[] = 'No characters listed.';
			}

			// - Warn if there is a death and no birth (nb: this may not be a good idea, some people have no DoB!)
			if ( ! empty( $check['death'] ) ) {
				if ( empty( $check['birth'] ) ) {
					$problems[] = 'Death date set without date of birth.';
				}
			}

			// - Wikipedia links should point to Wikipedia: "https://[language].wikipedia.org/" (props Jamie)
			if ( ! empty( $check['wiki'] ) && strpos( $check['wiki'], 'wikipedia.org/' ) === false ) {
				$problems[] = 'Wikipedia URL does not point to Wikipedia.';
			}

			// - IMDb IDs should be valid for the space they're in, e.g. "nm"
			// and digits for people (props Jamie)
			if ( ! empty( $check['imdb'] ) && ( new LWTV_Debug() )->validate_imdb( $check['imdb'] ) === false ) {
				$problems[] = 'IMDb ID is invalid (ex: nm12345).';
			}

			// Check for IMDb existing at all...
			if ( empty( $check['imdb'] ) ) {
				$problems[] = 'IMDb ID is not set.';
			}

			// - Instagram and Twitter usernames should follow whatever the
			// actual restrictions on those are  (props Jamie)
			// - If Instagram or Twitter usernames are the same format as IMDb IDs,
			// that's suspicious (props Jamie)
			if ( ! empty( $check['insta'] ) ) {
				// Limit - 30 symbols. Username must contains only letters, numbers, periods and underscores.
				if ( ( new LWTV_Debug() )->sanitize_social( $check['insta'], 'instagram' ) !== $check['insta'] ) {
					$problems[] = 'Instagram ID is invalid.';
				}
			}
			if ( ! empty( $check['twits'] ) ) {
				if ( ( new LWTV_Debug() )->sanitize_social( $check['twits'], 'twitter' ) !== $check['twits'] ) {
					$problems[] = 'Twitter ID is invalid.';
				}
			}

			// - "Website" links should *not* point to Wikipedia, since that
			// would make them Wikipedia links (props Jamie)
			if ( ! empty( $check['home'] ) ) {
				if ( strpos( $check['home'], 'wikipedia.org/' ) !== false ) {
					$problems[] = 'Homepage points to Wikipedia.';
				}
			}

			// - Duplicate Actor check - shouldn't end in -[NUMBER].
			$permalink_array = explode( '-', $check['dupes'] );
			$ends_with       = end( $permalink_array );
			// If it ends in a number, we have to check.
			if ( is_numeric( $ends_with ) ) {
				// See if an existing page without the -NUMBER exists (someone could rename themselves with numbers...).
				$possible = get_page_by_path( str_replace( '-' . $ends_with, '', $check['dupes'] ), OBJECT, 'actor' );
				if ( false !== $possible ) {
					$pos_imdb = get_post_meta( $possible->ID, 'lezactors_imdb', true );
					if ( isset( $pos_imdb ) && $pos_imdb === $check['imdb'] ) {
						$problems[] = 'Likely Dupe - Another Actor page with this name contains the same IMDb data.';
					}
				}
			}

			// If we added any problems, loop and add.
			if ( ! empty( $problems ) ) {
				$items[] = array(
					'url'     => get_permalink( $actor_id ),
					'id'      => $actor_id,
					'problem' => implode( ' ', $problems ),
				);
			}
		}

		// Save Transient
		set_transient( 'lwtv_debug_actor_problems', $items, WEEK_IN_SECONDS );

		// Update Options
		$option                   = get_option( 'lwtv_debugger_status' );
		$option['actor_problems'] = array(
			'name'  => 'Actors with Issues',
			'count' => ( ! empty( $items ) ) ? count( $items ) : 0,
			'last'  => time(),
		);
		$option['timestamp']      = time();
		update_option( 'lwtv_debugger_status', $option );

		return $items;
	}

	/**
	 * Find Actors with problems.
	 */
	public function find_actors_empty() {

		// Default
		$items = array();

		// Get all the actors
		$the_loop = ( new LWTV_Loops() )->post_type_query( 'post_type_actors' );

		if ( $the_loop->have_posts() ) {
			$actors = wp_list_pluck( $the_loop->posts, 'ID' );
			wp_reset_query();
		}

		foreach ( $actors as $actor_id ) {
			$problems = array();

			if ( ! has_post_thumbnail( $actor_id ) ) {
				$problems[] = 'No image found.';
			}

			if ( empty( get_the_content( '', false, $actor_id ) ) ) {
				$problems[] = 'No biography found.';
			}

			// If we added any problems, loop and add.
			if ( ! empty( $problems ) ) {
				$items[] = array(
					'url'     => get_permalink( $actor_id ),
					'id'      => $actor_id,
					'problem' => implode( ' ', $problems ),
				);
			}
		}

		// Save Transient
		set_transient( 'lwtv_debug_actor_empty', $items, WEEK_IN_SECONDS );

		// Update Options
		$option                = get_option( 'lwtv_debugger_status' );
		$option['actor_empty'] = array(
			'name'  => 'Incomplete Actors',
			'count' => count( $items ),
			'last'  => time(),
		);
		$option['timestamp']   = time();
		update_option( 'lwtv_debugger_status', $option );

		return $items;

	}


	/**
	 * Find Actors' WikiData
	 */
	public function check_actors_wikidata( $actors = 0 ) {

		$items = array();

		// If actors aren't a number or 0, AND they're not an array, we check everyone...
		if ( is_numeric( $actors ) && 0 !== $actors ) {
			$actors = array( $actors );
		} elseif ( ! is_array( $actors ) ) {
			// Get all the actors
			$the_loop = ( new LWTV_Loops() )->post_type_query( 'post_type_actors' );

			if ( $the_loop->have_posts() ) {
				$actors = wp_list_pluck( $the_loop->posts, 'ID' );
				wp_reset_query();
			}
		}

		if ( is_array( $actors ) ) {
			// For everyone in the list...
			foreach ( $actors as $actor_id ) {
				$items[ $actor_id ] = array(
					'id'   => $actor_id,
					'name' => get_the_title( $actor_id ),
				);

				// What we can check for
				$check_ours = array(
					'birth'     => get_post_meta( $actor_id, 'lezactors_birth', true ),
					'death'     => get_post_meta( $actor_id, 'lezactors_death', true ),
					'imdb'      => get_post_meta( $actor_id, 'lezactors_imdb', true ),
					'wikipedia' => get_post_meta( $actor_id, 'lezactors_wikipedia', true ),
					'instagram' => get_post_meta( $actor_id, 'lezactors_instagram', true ),
					'twitter'   => get_post_meta( $actor_id, 'lezactors_twitter', true ),
					'website'   => get_post_meta( $actor_id, 'lezactors_homepage', true ),
				);

				$permalink = basename( get_permalink( $actor_id ) );

				// Search for the actor:
				$search_name   = str_replace( ' ', '%20', get_the_title( $actor_id ) );
				$search_queery = 'https://www.wikidata.org/w/api.php?action=wbsearchentities&search=' . $search_name . '&language=en&format=json';
				$search_data   = wp_remote_get( $search_queery );

				// Check for errors.
				if ( ! is_wp_error( $search_data ) ) {
					$search_body = json_decode( $search_data['body'], true );
				}

				if ( isset( $search_body['search']['0']['id'] ) ) {
					// Set the WikiData ID
					$items[ $actor_id ]['wikidata'] = $search_body['search']['0']['id'];

					// Build the data
					$wiki_queery = 'https://www.wikidata.org/w/api.php?action=wbgetentities&ids=' . $search_body['search']['0']['id'] . '&format=json';
					$wiki_data   = wp_remote_get( $wiki_queery );
					if ( is_wp_error( $wiki_data ) ) {
						return;
					}
					$wiki_array = json_decode( $wiki_data['body'], true );

					$wiki_actor = array_shift( $wiki_array['entities'] );
					$wiki_link  = '';

					// If there's a wikipedia URL, let's get details.
					if ( '' !== $check_ours['wikipedia'] ) {
						$parsed_wiki = wp_parse_url( $check_ours['wikipedia'] );
						$parsed_url  = explode( '.', $parsed_wiki['host'] );
						$wiki_lang   = $parsed_wiki['host'][0];

						if ( isset( $wiki_actor['sitelinks'][ $wiki_lang . 'wiki' ] ) ) {
							$wiki_title = str_replace( ' ', '_', $wiki_actor['sitelinks'][ $wiki_lang . 'wiki' ]['title'] );
							$wiki_link  = 'https://' . $wiki_lang . '.wikipedia.org/wiki/' . $wiki_title;
						}
					} elseif ( isset( $wiki_actor['sitelinks']['enwiki'] ) ) {
						$wiki_title = str_replace( ' ', '_', $wiki_actor['sitelinks']['enwiki']['title'] );
						$wiki_link  = 'https://en.wikipedia.org/wiki/' . $wiki_title;
					};

					$check_wiki = array(
						'birth'     => ( isset( $wiki_actor['claims']['P569'] ) ) ? ( new LWTV_Debug() )->format_wikidate( $wiki_actor['claims']['P569'][0]['mainsnak']['datavalue']['value']['time'] ) : '',
						'death'     => ( isset( $wiki_actor['claims']['P570'] ) ) ? ( new LWTV_Debug() )->format_wikidate( $wiki_actor['claims']['P570'][0]['mainsnak']['datavalue']['value']['time'] ) : '',
						'wikipedia' => $wiki_link,
						'imdb'      => ( isset( $wiki_actor['claims']['P345'] ) ) ? $wiki_actor['claims']['P345'][0]['mainsnak']['datavalue']['value'] : '',
						'instagram' => ( isset( $wiki_actor['claims']['P2003'] ) ) ? $wiki_actor['claims']['P2003'][0]['mainsnak']['datavalue']['value'] : '',
						'twitter'   => ( isset( $wiki_actor['claims']['P2002'] ) ) ? $wiki_actor['claims']['P2002'][0]['mainsnak']['datavalue']['value'] : '',
						'website'   => ( isset( $wiki_actor['claims']['P856'] ) ) ? $wiki_actor['claims']['P856'][0]['mainsnak']['datavalue']['value'] : '',
					);

					foreach ( $check_ours as $item => $data ) {
						if ( $data === $check_wiki[ $item ] ) {
							$result = 'match';
						} elseif ( '' === $check_wiki[ $item ] ) {
							$result = 'n/a';
						} else {
							$result = array(
								'ours'     => $data,
								'wikidata' => $check_wiki[ $item ],
							);
						}

						$items[ $actor_id ][ $item ] = $result;
					}
				}
			}
		}

		if ( is_array( $items ) ) {
			// Save it all in the DB
			foreach ( $items as $one_item => $one_data ) {
				update_post_meta( $one_data['id'], '_lezactors_wikidata', $one_data );
			}
		}

		return $items;
	}

}

new LWTV_Debug_Actors();
