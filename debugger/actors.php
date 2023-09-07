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
	 *
	 * @return array $problems - array of problems. Can be empty.
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
				'dupes' => get_post_field( 'post_name', $actor_id ),
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

			// - Instagram and Twitter usernames should follow whatever the
			// actual restrictions on those are  (props Jamie)
			// - If Instagram or Twitter usernames are the same format as IMDb IDs,
			// that's suspicious (props Jamie)
			if ( ! empty( $check['insta'] ) ) {
				// Limit - 30 symbols. Username must contains only letters, numbers, periods and underscores.
				if ( ( new LWTV_Debug() )->sanitize_social( $check['insta'], 'instagram' ) !== $check['insta'] ) {
					$problems[] = 'Instagram ID is invalid -- ' . $check['insta'];
				} elseif ( ( new LWTV_Debug() )->validate_imdb( $check['insta'], 'actor' ) ) {
					// If instagram is IMDb, then it's wrong.
					delete_post_meta( $actor_id, 'lezactors_instagram' );
					$problems[] = 'Instagram ID was set as IMDb and has been removed - ' . $check['insta'];
				}
			}
			if ( ! empty( $check['twits'] ) ) {
				if ( ( new LWTV_Debug() )->sanitize_social( $check['twits'], 'twitter' ) !== $check['twits'] ) {
					$problems[] = 'Twitter ID is invalid -- ' . $check['insta'];
				} elseif ( ( new LWTV_Debug() )->validate_imdb( $check['twits'], 'actor' ) ) {
					// If Twitter is IMDb, then it's wrong.
					delete_post_meta( $actor_id, 'lezactors_twitter' );
					$problems[] = 'Twitter ID was set as IMDb and has been removed - ' . $check['twits'];
				}
			}

			// - "Website" links should *not* point to Wikipedia, since that
			// would make them Wikipedia links (props Jamie)
			if ( ! empty( $check['home'] ) ) {
				if ( strpos( $check['home'], 'wikipedia.org/' ) !== false ) {
					if ( empty( $check['wiki'] ) ) {
						// If there is no wiki set, move homepage to wiki and clear home page.
						update_post_meta( $actor_id, 'lezactors_wikipedia', $check['wiki'] );
						delete_post_meta( $actor_id, 'lezactors_homepage' );
					} elseif ( $check['wiki'] === $check['home'] ) {
						// If wiki === home page, delete home page.
						delete_post_meta( $actor_id, 'lezactors_homepage' );
					} else {
						// record problem
						$problems[] = 'Homepage points to Wikipedia - ' . sanitize_url( $check['home'] );
					}
				}
			}

			// - Duplicate Actor check - shouldn't end in -[NUMBER].

			$permalink_array = explode( '-', $check['dupes'] );
			$ends_with       = end( $permalink_array );
			// If it ends in a number, we have to check.
			if ( is_numeric( $ends_with ) ) {
				// See if an existing page without the -NUMBER exists (someone could rename themselves with numbers...).
				$possible = get_page_by_path( str_replace( '-' . $ends_with, '', $check['dupes'] ), OBJECT, 'post_type_actor' );
				if ( is_object( $possible ) && false !== $possible ) {
					// make sure the name doesn't have a year in parens!
					$pos_imdb = get_post_meta( $possible->ID, 'lezactors_imdb', true );
					if ( isset( $pos_imdb ) && $pos_imdb === $check['imdb'] ) {
						$problems[] = 'Duplicate? Another Actor page with this name contains the same IMDb data - ' . sanitize_url( $possible->url );
					}
				}
			}

			// If we added any problems, loop and add.
			if ( ! empty( $problems ) ) {
				$items[] = array(
					'url'     => get_permalink( $actor_id ),
					'id'      => $actor_id,
					'problem' => implode( '</br>', $problems ),
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
	 *
	 * @return array $problems - array of problems. Can be empty.
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
					'problem' => implode( '</br>', $problems ),
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
	 * Find all actors without IMDb Settings.
	 *
	 * @return array $problems - array of problems. Can be empty.
	 */
	public function find_actors_no_imdb() {
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

			$imdb = get_post_meta( $actor_id, 'lezactors_imdb', true );

			if ( empty( $imdb ) ) {
				// Check for IMDb existing at all...
				$problems[] = 'IMDb ID is not set.';
			} elseif ( ( new LWTV_Debug() )->validate_imdb( $imdb, 'actor' ) === false ) {
				// - IMDb IDs should be valid for the space they're in, e.g. "nm"
				// and digits for people (props Jamie).
				$problems[] = 'IMDb ID is invalid (ex: nm12345) -- ' . $imdb;
			}

			// If we added any problems, loop and add.
			if ( ! empty( $problems ) ) {
				$items[] = array(
					'url'     => get_permalink( $actor_id ),
					'id'      => $actor_id,
					'problem' => implode( '</br>', $problems ),
				);
			}
		}

		// Save Transient
		set_transient( 'lwtv_debug_actor_imdb', $items, WEEK_IN_SECONDS );

		// Update Options
		$option               = get_option( 'lwtv_debugger_status' );
		$option['actor_imdb'] = array(
			'name'  => 'Actors without IMDb',
			'count' => ( ! empty( $items ) ) ? count( $items ) : 0,
			'last'  => time(),
		);
		$option['timestamp']  = time();
		update_option( 'lwtv_debugger_status', $option );

		return $items;
	}

	/**
	 * Scan Actors' WikiData
	 *
	 * Validate the wikidata matches our data.
	 *
	 * @param int|array $actors Individual ID or Array of IDs, or empty.
	 *
	 * @return array    $items Result of checks.
	 */
	public function check_actors_wikidata( $actors = 0 ) {

		$items = array();

		// If actors aren't a number or 0, AND they're not an array, we check everyone...
		if ( is_numeric( $actors ) && 0 !== $actors && ! is_array( $actors ) ) {
			$actors = array( $actors );
		} else {
			// Get all the actors
			$the_loop = ( new LWTV_Loops() )->post_type_query( 'post_type_actors' );

			if ( $the_loop->have_posts() ) {
				$actors = wp_list_pluck( $the_loop->posts, 'ID' );
				wp_reset_query();
			}
		}

		// Since this now an array no matter what, we search it all.
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
				'facebook'  => str_replace( 'https://facebook.com/', '', get_post_meta( $actor_id, 'lezactors_facebook', true ) ),
				'website'   => get_post_meta( $actor_id, 'lezactors_homepage', true ),
			);

			$permalink = basename( get_permalink( $actor_id ) );
			$language  = 'en';

			// Search for the actor, using the Q-ID if it's set.
			$wikidata_id = get_post_meta( $actor_id, 'lezactors_wikidata', true );
			if ( ! empty( $wikidata_id ) ) {
				$search_name = $wikidata_id;
			} else {
				$search_name = str_replace( ' ', '%20', get_the_title( $actor_id ) );
			}

			// Pick language based on existing WikiPedia link.
			if ( ! empty( $check_ours['wikipedia'] ) ) {
				$wikiurl  = wp_parse_url( $check_ours['wikipedia'] );
				$wikihost = explode( '.', $wikiurl['host'] );
				$language = $wikihost[0];
			}

			$search_queery = 'https://www.wikidata.org/w/api.php?action=wbsearchentities&search=' . $search_name . '&language=' . $language . '&format=json';
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
				}

				$check_wiki = array(
					'birth'     => ( isset( $wiki_actor['claims']['P569'] ) ) ? ( new LWTV_Debug() )->format_wikidate( $wiki_actor['claims']['P569'][0]['mainsnak']['datavalue']['value']['time'] ) : '',
					'death'     => ( isset( $wiki_actor['claims']['P570'] ) ) ? ( new LWTV_Debug() )->format_wikidate( $wiki_actor['claims']['P570'][0]['mainsnak']['datavalue']['value']['time'] ) : '',
					'wikipedia' => $wiki_link,
					'imdb'      => ( isset( $wiki_actor['claims']['P345'] ) ) ? $wiki_actor['claims']['P345'][0]['mainsnak']['datavalue']['value'] : '',
					'instagram' => ( isset( $wiki_actor['claims']['P2003'] ) ) ? $wiki_actor['claims']['P2003'][0]['mainsnak']['datavalue']['value'] : '',
					'twitter'   => ( isset( $wiki_actor['claims']['P2002'] ) ) ? $wiki_actor['claims']['P2002'][0]['mainsnak']['datavalue']['value'] : '',
					'facebook'  => ( isset( $wiki_actor['claims']['P2013'] ) ) ? $wiki_actor['claims']['P2013'][0]['mainsnak']['datavalue']['value'] : '',
					'website'   => ( isset( $wiki_actor['claims']['P856'] ) ) ? $wiki_actor['claims']['P856'][0]['mainsnak']['datavalue']['value'] : '',
				);

				foreach ( $check_ours as $item => $data ) {
					if ( $data === $check_wiki[ $item ] ) {
						$result = 'match';
					} elseif ( '' === $check_wiki[ $item ] ) {
						$result = 'n/a';
					} else {
						if ( 'facebook' === $item ) {
							$check_wiki[ $item ] = 'https://facebook.com/' . $check_wiki[ $item ];
						}
						$result = array(
							'ours'     => $data,
							'wikidata' => $check_wiki[ $item ],
						);
					}

					$items[ $actor_id ][ $item ] = $result;
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
