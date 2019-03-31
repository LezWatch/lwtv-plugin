<?php
/**
 * Name: Show Calculations
 * Description: Calculate various data points for shows
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Shows_Calculate
 *
 * @since 2.1.0
 */

class LWTV_Shows_Calculate {

	/**
	 * Calculate show rating.
	 */
	public static function show_score( $post_id ) {

		if ( ! isset( $post_id ) ) {
			return;
		}

		// Get base ratings
		// Multiply by 3 for a max of 30
		$realness   = min( (int) get_post_meta( $post_id, 'lezshows_realness_rating', true ), 5 );
		$quality    = min( (int) get_post_meta( $post_id, 'lezshows_quality_rating', true ), 5 );
		$screentime = min( (int) get_post_meta( $post_id, 'lezshows_screentime_rating', true ), 5 );
		$score      = ( $realness + $quality + $screentime ) * 2;

		// Add in Thumb Score Rating: 10, 5, 0, -10
		switch ( get_post_meta( $post_id, 'lezshows_worthit_rating', true ) ) {
			case 'Yes':
				$score += 10;
				break;
			case 'Meh':
				$score += 5;
				break;
			case 'TBD':
				$score += 0;
				break;
			case 'No':
				$score -= 10;
				break;
			default:
				$score = $score;
				break;
		}

		// Add in Star Rating: 20, 10, 5, -15
		$star_terms = get_the_terms( $post_id, 'lez_stars' );
		$color      = ( ! empty( $star_terms ) && ! is_wp_error( $star_terms ) ) ? $star_terms[0]->slug : get_post_meta( $post_id, 'lez_stars', true );

		switch ( $color ) {
			case 'gold':
				$score += 20;
				break;
			case 'silver':
				$score += 10;
				break;
			case 'bronze':
				$score += 5;
				break;
			case 'anti':
				$score -= 15;
				break;
		}

		// Trigger Warning: -5, -10, -15
		$trigger_terms = get_the_terms( $post_id, 'lez_triggers' );
		$trigger       = ( ! empty( $trigger_terms ) && ! is_wp_error( $trigger_terms ) ) ? $trigger_terms[0]->slug : get_post_meta( $post_id, 'lezshows_triggerwarning', true );
		switch ( $trigger ) {
			case 'on':
			case 'high':
				$score -= 15;
				break;
			case 'med':
			case 'medium':
				$score -= 10;
				break;
			case 'low':
				$score -= 5;
				break;
		}

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
	 * @param int $post_id The post ID.
	 */
	public static function count_queers( $post_id, $type = 'count' ) {
		$type_array = array( 'count', 'none', 'dead', 'queer-irl', 'score' );

		// If this isn't a show post, return nothing
		if ( 'post_type_shows' !== get_post_type( $post_id ) || ! in_array( esc_attr( $type ), $type_array, true ) ) {
			return;
		}

		// Here we need to break down the scores
		if ( 'score' === $type ) {
			$char_score      = 0;
			$all_chars       = LWTV_CPT_Characters::list_characters( $post_id, 'count' );
			$chars_regular   = LWTV_CPT_Characters::get_chars_for_show( $post_id, $all_chars, 'regular' );
			$chars_recurring = LWTV_CPT_Characters::get_chars_for_show( $post_id, $all_chars, 'recurring' );
			$chars_guest     = LWTV_CPT_Characters::get_chars_for_show( $post_id, $all_chars, 'guest' );

			// Points: Regular = 5; Recurring = 2; Guests = 1
			$char_score = ( count( $chars_regular ) * 5 ) + ( count( $chars_recurring ) * 2 ) + count( $chars_guest );

			// Bonuses and Demerits
			// Bonuses:  queer irl = 4pts; no cliches = 2pt; trans played by non-trans = 2pts
			// Demerits: dead = -3pts; trans played by non-trans = -2pts
			$queer_irl   = ( max( 0, LWTV_CPT_Characters::list_characters( $post_id, 'queer-irl' ) ) * 10 );
			$no_cliches  = max( 0, LWTV_CPT_Characters::list_characters( $post_id, 'none' ) * 5 );
			$the_dead    = ( max( 0, LWTV_CPT_Characters::list_characters( $post_id, 'dead' ) ) * -5 );
			$trans_chars = max( 0, LWTV_CPT_Characters::list_characters( $post_id, 'trans' ) );
			$trans_irl   = max( 0, LWTV_CPT_Characters::list_characters( $post_id, 'trans-irl' ) );
			$trans_score = 0;
			if ( $trans_irl < $trans_chars ) {
				$trans_score = ( ( $trans_chars - $trans_irl ) * -5 );
			} else {
				$trans_score = $trans_chars * 10;
			}

			// Add it all together (negatives are taken care of above)
			$char_score = $char_score + $queer_irl + $no_cliches + $the_dead + $trans_score;

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

			// Finally make sure we're between 0 and 100
			$char_score = ( $char_score > 100 ) ? 100 : $char_score;
		}

		// Give back Queers!
		switch ( $type ) {
			case 'score':
				$return = $char_score;
				break;
			case 'count':
				$return = LWTV_CPT_Characters::list_characters( $post_id, 'count' );
				break;
			case 'dead':
				$return = LWTV_CPT_Characters::list_characters( $post_id, 'dead' );
				break;
			case 'none':
				$return = LWTV_CPT_Characters::list_characters( $post_id, 'none' );
				break;
			case 'queer-irl':
				$return = LWTV_CPT_Characters::list_characters( $post_id, 'queer-irl' );
				break;
		}

		return $return;
	}

	/**
	 * Calculate show tropes score.
	 */
	public static function show_tropes_score( $post_id ) {

		$score        = 0;
		$tropes       = wp_get_post_terms( $post_id, 'lez_tropes', true );
		$count_tropes = ( $tropes ) ? count( $tropes ) : 0;

		// Good tropes are always good.
		// Maybe tropes are only good IF there isn't Queer-for-Ratings
		$good_tropes  = array( 'happy-ending', 'everyones-queer' );
		$maybe_tropes = array( 'big-queer-wedding', 'coming-out', 'subtext' );
		$bad_tropes   = array( 'queerbashing', 'in-prison', 'queerbaiting', 'big-bad-queers' );
		$ploy_tropes  = array( 'queer-for-ratings', 'queer-laughs', 'happy-then-not', 'erasure', 'subtext' );

		if ( has_term( 'none', 'lez_tropes', $post_id ) ) {
			// No tropes: 80
			$score = 80;
		} else {
			// Calculate how many good tropes a show has
			$has_tropes = array(
				'good'  => 0,
				'maybe' => 0,
				'bad'   => 0,
				'ploy'  => 0,
			);
			foreach ( $good_tropes as $trope ) {
				if ( has_term( $trope, 'lez_tropes', $post_id ) ) {
					$has_tropes['good']++;
				}
			}
			// Calculate Maybe Good Tropes
			foreach ( $maybe_tropes as $trope ) {
				if ( has_term( $trope, 'lez_tropes', $post_id ) ) {
					$has_tropes['maybe']++;
				}
			}
			// Calculate Bad Tropes
			foreach ( $bad_tropes as $trope ) {
				if ( has_term( $trope, 'lez_tropes', $post_id ) ) {
					$has_tropes['bad']++;
				}
			}
			// Calculate Ploy Tropes
			foreach ( $ploy_tropes as $trope ) {
				if ( has_term( $trope, 'lez_tropes', $post_id ) ) {
					$has_tropes['ploy']++;
				}
			}

			$base_score     = ( $has_tropes['good'] + $has_tropes['maybe'] - $has_tropes['ploy'] - $has_tropes['bad'] );
			$counted_tropes = $has_tropes['good'] + $has_tropes['maybe'] + $has_tropes['ploy'] + $has_tropes['bad'];

			if ( $base_score > 0 ) {
				$score = ( ( $base_score / $counted_tropes ) * 100 );
				// Dead Queers: remove one-third of the score
				if ( has_term( 'dead-queers', 'lez_tropes', $post_id ) ) {
					$score = ( $score * .66 );
				}
			} else {
				$score = 0;
			}
		}

		// Sanity Check
		$score = ( $score > 100 ) ? 100 : $score;
		$score = ( $score < 0 ) ? 0 : $score;

		return $score;
	}

	/**
	 * Calculate show character score.
	 */
	public static function show_character_score( $post_id ) {

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

		// Update post meta for counts
		// NOTE: This cannot be an array becuase of how it's used for Facet later on.
		// MIKA! SERIOUSLY! NO!
		update_post_meta( $post_id, 'lezshows_char_count', $number_chars );
		update_post_meta( $post_id, 'lezshows_dead_count', $number_dead );

		return $score;
	}

	/**
	 * Calculate show character data.
	 */
	public static function show_character_data( $post_id ) {

		// If this isn't a show post, return nothing
		if ( get_post_type( $post_id ) !== 'post_type_shows' ) {
			return;
		}

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

		// Loop to get the list of characters
		$charactersloop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $post_id, 'LIKE' );

		// Store as array to defeat some stupid with counting and prevent querying the database too many times
		if ( $charactersloop->have_posts() ) {
			while ( $charactersloop->have_posts() ) {

				$charactersloop->the_post();
				$char_id     = get_the_ID();
				$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true );

				if ( '' !== $shows_array && 'publish' === get_post_status( $char_id ) ) {
					foreach ( $shows_array as $char_show ) {
						// phpcs:ignore WordPress.PHP.StrictComparisons
						if ( $char_show['show'] == $post_id ) {
							// Bump the array for this role
							$role_data[ $char_show['type'] ]++;

							// Now we'll sort gender and stuff...
							foreach ( $valid_taxes as $title => $taxonomy ) {
								$this_term = get_the_terms( $char_id, $taxonomy, true );
								if ( $this_term && ! is_wp_error( $this_term ) ) {
									foreach ( $this_term as $term ) {
										$tax_data[ $title ][ $term->slug ]++;
									}
								}
							}
						}
					}
				}
			}
			wp_reset_query();
		}

		// Update the roles
		update_post_meta( $post_id, 'lezshows_char_roles', $role_data );

		// Update the taxonomies
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
	public static function do_the_math( $post_id ) {

		// If this isn't a show, we bail.
		if ( 'post_type_shows' !== get_post_type( $post_id ) ) {
			return;
		}

		// Get the ratings
		$score_show_rating = self::show_score( $post_id );
		$score_chars_total = self::show_character_score( $post_id );
		$score_chars_alive = $score_chars_total['alive'];
		$score_chars_score = $score_chars_total['score'];
		$score_show_tropes = self::show_tropes_score( $post_id );

		// Generate character data
		self::show_character_data( $post_id );

		// Calculate the full score
		$calculate = ( $score_show_rating + $score_chars_alive + $score_chars_score + $score_show_tropes ) / 4;

		// Add Intersectionality Bonus
		// If you do good with intersectionality you can have more points up to 15
		$count_inters = 0;
		$intersection = get_the_terms( $post_id, 'lez_intersections' );
		if ( is_array( $intersection ) ) {
			$count_inters = count( $intersection );
		}
		$calculate += min( ( $count_inters * 3 ), 15 );

		// Keep it between 0 and 100
		if ( $calculate > 100 ) {
			$calculate = 100;
		}
		if ( $calculate < 0 ) {
			$calculate = 0;
		}

		// Update the meta
		update_post_meta( $post_id, 'lezshows_the_score', $calculate );
	}
}

new LWTV_Shows_Calculate();
