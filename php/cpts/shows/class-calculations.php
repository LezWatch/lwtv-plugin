<?php
/**
 * Name: Show Calculations
 * Description: Calculate various data points for shows
 */

namespace LWTV\CPTs\Shows;

class Calculations {

	/**
	 * Calculate show rating.
	 *
	 * @param int  $post_id Post ID
	 * @return int          Show score
	 */
	public function show_score( $post_id ) {

		// If this is not a valid show post type, we skip.
		if ( ! isset( $post_id ) || 'post_type_shows' !== get_post_type( $post_id ) ) {
			return;
		}

		// Set initial score:
		$score = 0;

		// Base Ratings: Multiply by 3 for a max of 30
		$realness   = min( (int) get_post_meta( $post_id, 'lezshows_realness_rating', true ), 5 );
		$quality    = min( (int) get_post_meta( $post_id, 'lezshows_quality_rating', true ), 5 );
		$screentime = min( (int) get_post_meta( $post_id, 'lezshows_screentime_rating', true ), 5 );
		$score     .= ( $realness + $quality + $screentime ) * 3;

		// Thumb Score Rating: 10, 5, 0, -10
		$worth_it    = get_post_meta( $post_id, 'lezshows_worthit_rating', true );
		$worth_score = array(
			'Yes' => 10,
			'Meh' => 5,
			'No'  => -10,
			'TBD' => 0,
		);
		$score      += ( key_exists( $worth_it, $worth_score ) ) ? $worth_score[ $worth_it ] : 0;

		// Star Rating: 20, 10, 5, -15
		$star_terms = get_the_terms( $post_id, 'lez_stars' );
		$stars      = ( ! empty( $star_terms ) && ! is_wp_error( $star_terms ) ) ? $star_terms[0]->slug : get_post_meta( $post_id, 'lez_stars', true );
		$star_score = array(
			'gold'   => 20,
			'silver' => 10,
			'bronze' => 5,
			'anti'   => -15,
		);
		$score     += ( key_exists( $stars, $star_score ) ) ? $star_score[ $stars ] : 0;

		// Trigger Warning: -5, -10, -15
		$trigger_terms = get_the_terms( $post_id, 'lez_triggers' );
		$trigger       = ( ! empty( $trigger_terms ) && ! is_wp_error( $trigger_terms ) ) ? $trigger_terms[0]->slug : get_post_meta( $post_id, 'lezshows_triggerwarning', true );
		$trigger_score = array(
			'on'     => 15,
			'high'   => 15,
			'med'    => 10,
			'medium' => 10,
			'low'    => 5,
		);
		$score        += ( key_exists( $trigger, $trigger_score ) ) ? $trigger_score[ $trigger ] : 0;

		// Shows We Love: 40 points
		if ( 'on' === get_post_meta( $post_id, 'lezshows_worthit_show_we_love', true ) ) {
			$score += 40;
		}

		return $score;
	}

	/*
	 * Count Queers
	 *
	 * This will update the metakeys on save
	 *
	 * @param  int $post_id  The post ID.
	 * @return int           The number of queers
	 */
	public function count_queers( $post_id, $type = 'count' ) {

		if ( ! isset( $post_id ) ) {
			return;
		}

		$type_array = array( 'count', 'none', 'dead', 'queer-irl', 'score' );

		// If this isn't one of the above types, return.
		if ( ! in_array( $type, $type_array, true ) ) {
			return;
		}

		// before we do the math, let's see if we have any characters:
		$raw_char_count = lwtv_plugin()->get_characters_list( $post_id, 'count' );

		// Empty raw? Return 0.
		if ( empty( $raw_char_count ) ) {
			return 0;
		}

		$raw_char_count = ( ! is_array( $raw_char_count ) ) ? array( $raw_char_count ) : $raw_char_count;
		$char_count     = count( $raw_char_count );

		// No characters? It's a zero.
		if ( 0 === $char_count ) {
			return 0;
		}

		// Here we need to break down the scores:
		if ( 'score' === $type ) {
			$char_score = 0;

			// If the count for all characters is 0, we don't need to run this.
			if ( 0 !== $char_count ) {
				$chars_regular   = lwtv_plugin()->get_chars_for_show( $post_id, 'count', 'regular' );
				$chars_recurring = lwtv_plugin()->get_chars_for_show( $post_id, 'count', 'recurring' );
				$chars_guest     = lwtv_plugin()->get_chars_for_show( $post_id, 'count', 'guest' );

				// Points: Regular = 5; Recurring = 2; Guests = 1
				$char_score = ( count( $chars_regular ) * 5 ) + ( count( $chars_recurring ) * 2 ) + count( $chars_guest );

				// TODO: Consider ratio-ing - if there are a lot of guests and no regulars, that's bad, but if your guests
				// are closer in line to the number of regulars... Maybe 4 guests to 1 regular?

				// Bonuses and Demerits
				// Bonuses:  queer irl = 4pts; no cliches = 2pt; trans played by non-trans = 2pts
				// Demerits: dead = -3pts; trans played by non-trans = -2pts
				$queer_irl   = ( max( 0, lwtv_plugin()->get_characters_list( $post_id, 'queer-irl' ) ) * 10 );
				$no_cliches  = ( max( 0, lwtv_plugin()->get_characters_list( $post_id, 'none' ) ) * 5 );
				$the_dead    = ( max( 0, lwtv_plugin()->get_characters_list( $post_id, 'dead' ) ) * -5 );
				$trans_chars = max( 0, lwtv_plugin()->get_characters_list( $post_id, 'trans' ) );
				$trans_irl   = max( 0, lwtv_plugin()->get_characters_list( $post_id, 'trans-irl' ) );
				$trans_score = 0;
				if ( $trans_irl < $trans_chars ) {
					$trans_score = ( ( $trans_chars - $trans_irl ) * -5 );
				} else {
					$trans_score = $trans_chars * 10;
				}

				// Add it all together (negatives are taken care of above)
				$char_score = $char_score + $queer_irl + $no_cliches + $the_dead + $trans_score;
			}

			// If the score is 0, nothing else needs be done.
			if ( 0 !== $char_score ) {
				// Adjust scores based on type of series
				if ( has_term( 'movie', 'lez_formats', $post_id ) ) {
					// Movies have a low bar, since they have low stakes
					$char_score = ( $char_score / 2 );
				} elseif ( has_term( 'mini-series', 'lez_formats', $post_id ) ) {
					// Mini-Series similarly have a small run
					$char_score = ( $char_score / 1.5 );
				} elseif ( has_term( 'web-series', 'lez_formats', $post_id ) ) {
					// WebSeries tend to be more daring, but again, low stakes.
					$char_score = ( $char_score / 1.25 );
				}
			}

			// Finally make sure we're between 0 and 100
			$char_score = ( $char_score > 100 ) ? 100 : $char_score;
		}

		// Give back Queers!
		switch ( $type ) {
			case 'score':
				$return = $char_score;
				break;
			case 'count':
				$return = $char_count;
				break;
			case 'dead':
				$return = lwtv_plugin()->get_characters_list( $post_id, 'dead' );
				break;
			case 'none':
				$return = lwtv_plugin()->get_characters_list( $post_id, 'none' );
				break;
			case 'queer-irl':
				$return = lwtv_plugin()->get_characters_list( $post_id, 'queer-irl' );
				break;
		}

		return $return;
	}

	/**
	 * Calculate show tropes score.
	 */
	public function show_tropes_score( $post_id ) {

		if ( ! isset( $post_id ) ) {
			return;
		}

		$score        = 0;
		$tropes       = wp_get_post_terms( $post_id, 'lez_tropes', true );
		$count_tropes = ( $tropes ) ? count( $tropes ) : 0;
		$has_dead     = ( has_term( 'dead-queers', 'lez_tropes', $post_id ) ) ? true : false;
		$is_happy_end = ( has_term( 'happy-ending', 'lez_tropes', $post_id ) ) ? true : false;

		// Death Override Checker.
		$override = get_post_meta( $post_id, 'lezshows_byq_override', true );
		if ( isset( $override ) && ! empty( $override ) && 'yes' === $override ) {
			$has_dead = false;
		}

		// Good tropes are always good.
		// Maybe tropes are only good IF there isn't Queer-for-Ratings
		$good_tropes  = array( 'happy-ending', 'everyones-queer' );
		$maybe_tropes = array( 'big-queer-wedding', 'coming-out', 'subtext' );
		$bad_tropes   = array( 'queerbashing', 'in-prison', 'queerbaiting', 'big-bad-queers' );
		$ploy_tropes  = array( 'queer-for-ratings', 'queer-laughs', 'happy-then-not', 'erasure', 'subtext', 'queer-of-the-week', 'background-queers' );

		// If there a no tropes, we have a default of 80.
		if ( ( 0 === $count_tropes ) || has_term( 'none', 'lez_tropes', $post_id ) ) {
			// No tropes: 80
			$score = 80;
		} else {
			// Base Tropes Score
			$has_tropes = array(
				'good'  => 0,
				'maybe' => 0,
				'bad'   => 0,
				'ploy'  => 0,
				'any'   => 0,
			);
			// Calculate good tropes
			foreach ( $good_tropes as $trope ) {
				if ( has_term( $trope, 'lez_tropes', $post_id ) ) {
					++$has_tropes['good'];
					++$has_tropes['any'];
				}
			}
			// Calculate Maybe Good Tropes
			foreach ( $maybe_tropes as $trope ) {
				if ( has_term( $trope, 'lez_tropes', $post_id ) ) {
					++$has_tropes['maybe'];
					++$has_tropes['any'];
				}
			}
			// Calculate Bad Tropes
			foreach ( $bad_tropes as $trope ) {
				if ( has_term( $trope, 'lez_tropes', $post_id ) ) {
					++$has_tropes['bad'];
					++$has_tropes['any'];
				}
			}
			// Calculate Ploy Tropes
			foreach ( $ploy_tropes as $trope ) {
				if ( has_term( $trope, 'lez_tropes', $post_id ) ) {
					++$has_tropes['ploy'];
					++$has_tropes['any'];
				}
			}

			// Pause for C Shows
			if ( 0 === $has_tropes['any'] ) {
				// If a show has NO good/maybe/bad/ploy tropes, it gets a C
				$score = 70;
			} else {
				// Most shows need math!
				$base_score     = ( $has_tropes['good'] + $has_tropes['maybe'] - $has_tropes['ploy'] - $has_tropes['bad'] );
				$counted_tropes = $has_tropes['good'] + $has_tropes['maybe'] + $has_tropes['ploy'] + $has_tropes['bad'];

				if ( $base_score > 0 ) {
					$score = ( ( $base_score / $counted_tropes ) * 100 );
				} else {
					$score = 0;
				}
			}
		}

		// Add Intersectionality Bonus
		// If you do good with intersectionality you can have more points up to 15
		$count_inters = 0;
		$intersection = get_the_terms( $post_id, 'lez_intersections' );
		if ( is_array( $intersection ) ) {
			$count_inters = count( $intersection );
			$score       += min( ( $count_inters * 3 ), 15 );
		}

		// Sanity Check: Below 0?
		$score = ( $score < 0 ) ? 0 : $score;

		// Death Deductions
		if ( 0 !== $score && $has_dead && ! $is_happy_end ) {
			// If there are dead WITHOUT happy-ending, drop by a third.
			$score = ( $score * .66 );
		} elseif ( 0 !== $score && $has_dead && $is_happy_end ) {
			// If there are dead WITH happy-ending, drop by a quarter
			$score = ( $score * .75 );
		}

		// Sanity Check: Still above 100?
		$score = ( $score > 100 ) ? 100 : $score;

		return $score;
	}

	/**
	 * Calculate show character score.
	 *
	 * NO MATTER WHAT YOU THINK the post counts HAVE to be two separate meta fields.
	 * Otherwise you get weird issues with FacetWP.
	 *
	 * Attempts: 4
	 */
	public function show_character_score( $post_id ) {

		// Base Score
		$score = array(
			'alive' => 0,
			'score' => 0,
		);

		// Count characters
		$number_chars = max( 0, self::count_queers( $post_id, 'count' ) );
		$number_dead  = max( 0, self::count_queers( $post_id, 'dead' ) );

		// If there are no chars, the score will be zero, so bail early.
		if ( 0 !== $number_chars ) {
			$score['alive'] = ( ( ( $number_chars - $number_dead ) / $number_chars ) * 100 );
			$score['score'] = self::count_queers( $post_id, 'score' );
		}

		// Update post meta for counts (NO YOU CANNOT MAKE THIS AN ARRAY)
		update_post_meta( $post_id, 'lezshows_char_count', $number_chars );
		update_post_meta( $post_id, 'lezshows_dead_count', $number_dead );

		return $score;
	}

	/**
	 * Calculate show character data.
	 */
	public function show_character_data( $post_id ) {

		// What role each character has
		$role_data = array(
			'regular'   => 0,
			'recurring' => 0,
			'guest'     => 0,
		);

		// Create a massive array of all the terms we care about...
		$valid_taxes = array(
			'gender'    => 'lez_gender',
			'sexuality' => 'lez_sexuality',
			'romantic'  => 'lez_romantic',
		);
		$tax_data    = array();

		foreach ( $valid_taxes as $title => $taxonomy ) {
			$terms = get_terms( $taxonomy );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$tax_data[ $title ] = array();
				foreach ( $terms as $term ) {
					$tax_data[ $title ][ $term->slug ] = 0;
				}
			}
		}

		// Get array of characters (by ID)
		$characters     = lwtv_plugin()->get_characters_list( $post_id, 'query' );
		$new_characters = array();

		if ( is_array( $characters ) ) {
			foreach ( $characters as $char_id ) {

				// If the character isn't published, skip it.
				if ( get_post_status( $char_id ) !== 'publish' ) {
					continue;
				}

				$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true );

				if ( '' !== $shows_array && 'publish' === get_post_status( $char_id ) ) {
					foreach ( $shows_array as $char_show ) {

						// Remove the array if it's there.
						if ( is_array( $char_show['show'] ) ) {
							$char_show['show'] = $char_show['show'][0];
						}

						// phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
						if ( $char_show['show'] == $post_id ) {
							// Bump the array for this role
							++$role_data[ $char_show['type'] ];
							$new_characters[] = $char_id;

							// Now we'll sort gender and stuff...
							foreach ( $valid_taxes as $title => $taxonomy ) {
								$this_term = get_the_terms( $char_id, $taxonomy, true );
								if ( $this_term && ! is_wp_error( $this_term ) ) {
									foreach ( $this_term as $term ) {
										++$tax_data[ $title ][ $term->slug ];
									}
								}
							}
						}
					}
				}
			}
		}

		// Update the roles scores
		update_post_meta( $post_id, 'lezshows_char_roles', $role_data );

		// Update the characters
		update_post_meta( $post_id, 'lezshows_char_list', $new_characters );

		/**
		 * Update the taxonomies
		 *  - lezshows_char_sexuality
		 *  - lezshows_char_gender
		 *  - lezshows_char_romantic
		 */
		foreach ( $valid_taxes as $title => $taxonomy ) {
			update_post_meta( $post_id, 'lezshows_char_' . $title, $tax_data[ $title ] );
		}
	}

	/**
	 * do_the_math function.
	 *
	 * This will update the following metakeys on save:
	 *  - lezshows_char_count      Number of characters
	 *  - lezshows_dead_count      Number of dead characters
	 *  - lezshows_the_score       Score of show data
	 *
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	public function do_the_math( $post_id ) {

		// Delete the data.
		$delete_array = array( 'lezshows_char_list', 'lezshows_char_count', 'lezshows_dead_list', 'lezshows_dead_count', 'lezshows_the_score' );
		foreach ( $delete_array as $delete_meta ) {
			delete_post_meta( $post_id, $delete_meta );
		}

		// Get the ratings
		$score_show_rating = self::show_score( $post_id );
		$score_show_tropes = self::show_tropes_score( $post_id );
		$score_chars_total = self::show_character_score( $post_id );
		$score_chars_alive = $score_chars_total['alive'];
		$score_chars_score = $score_chars_total['score'];

		// Generate character data
		self::show_character_data( $post_id );

		// Calculate the full score
		$calculate = ( $score_show_rating + $score_show_tropes + $score_chars_alive + $score_chars_score ) / 4;

		// Keep it between 0 and 100
		$calculate = ( $calculate > 100 ) ? 100 : $calculate;
		$calculate = ( $calculate < 0 ) ? 0 : $calculate;

		// Update the score meta. The rest are done.
		update_post_meta( $post_id, 'lezshows_the_score', $calculate );

		// Update 3rd party scores
		lwtv_plugin()->update_grading_scores( $post_id );

		// Cheat and update the show 'on-air' ness.
		$on_air   = 'no';
		$airdates = get_post_meta( $post_id, 'lezshows_airdates', true );
		if ( ! isset( $airdates['finish'] ) || 'current' === lcfirst( $airdates['finish'] ) || $airdates['finish'] >= gmdate( 'Y' ) ) {
			$on_air = 'yes';
		}
		update_post_meta( $post_id, 'lezshows_on_air', $on_air );
	}
}
