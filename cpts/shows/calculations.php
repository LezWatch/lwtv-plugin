<?php
/**
 * Name: Show Calculations
 * Description: Calculate various data points for shows
 */

if ( ! defined('WPINC' ) ) die;

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

		if ( !isset( $post_id ) ) return;

		// Get base ratings
		// Multiply by 3 for a max of 45
		$realness   = min( (int) get_post_meta( $post_id, 'lezshows_realness_rating', true) , 5 );
		$quality    = min( (int) get_post_meta( $post_id, 'lezshows_quality_rating', true) , 5 );
		$screentime = min( (int) get_post_meta( $post_id, 'lezshows_screentime_rating', true) , 5 );
		$score  = ( $realness + $quality + $screentime ) * 3;
		
		// Add in Thumb Score Rating
		switch ( get_post_meta( $post_id, 'lezshows_worthit_rating', true ) ) {
			case "Yes":
				$score += 10;
				break;
			case "Meh":
				$score += 5;
			case "No":
				$score -= 10;
				break;
			default:
				$score += 0;
		}

		// Add in Star Rating
		$star_terms = get_the_terms( $post_id, 'lez_stars' );
		$color = ( !empty( $star_terms ) && !is_wp_error( $star_terms ) )? $star_terms[0]->slug : get_post_meta( $post_id, 'lez_stars', true );
		
		switch ( $color ) {
			case "gold":
				$score += 15;
				break;
			case "silver":
				$score += 10;
				break;
			case "bronze":
				$score += 5;
				break;
			case "anti":
				$score -= 10;
				break;
			default:
				$score += 0;
		}

		// Trigger Warning
		$trigger_terms = get_the_terms( $post_id, 'lez_triggers' );
		$trigger = ( !empty( $trigger_terms ) && !is_wp_error( $trigger_terms ) )? $trigger_terms[0]->slug : get_post_meta( $post_id, 'lezshows_triggerwarning', true );
		switch ( $trigger ) {
			case "on":
			case "high":
				$score -= 15;
				break;
			case "med":
			case "medium":
				$score -= 10;
				break;
			case "low":
				$score -= 5;
				break;
			default:
				$score -= 0;
		}

		// Trope Math
		$count_tropes = count( wp_get_post_terms( $post_id, 'lez_tropes' ) );
		$good_tropes  = array( 'happy-ending', 'everyones-queer', 'coming-out' );
		
		if ( has_term( 'none', 'lez_tropes', $post_id ) ) $score += 15;

		foreach ( $good_tropes as $trope ) {
			$value = ( $count_tropes == 1 )? 10 : 5;
			if ( has_term( $trope, 'lez_tropes', $post_id ) ) $score += $value;
		}

		if ( has_term( 'dead-queers', 'lez_tropes', $post_id ) ) $score -= 15;

		// Shows We Love
		if ( get_post_meta( $post_id, 'lezshows_worthit_show_we_love', true ) == 'on' ) 
			$score += 15;

		return $score;
	}

	/*
	 * Count Queers
	 *
	 * This will update the metakey 'lezshows_char_count' on save
	 *
	 * @param int $post_id The post ID.
	 */
	public static function count_queers( $post_id , $type = 'count' ) {

		// If this isn't a show post, return nothing
		if ( get_post_type( $post_id ) !== 'post_type_shows' ) return;

		// If there's no valid type, return nothing
		$type_array = array ( 'count', 'none', 'dead', 'queer-irl' );
		if ( !in_array( esc_attr( $type ), $type_array ) ) return;

		// Loop to get the list of characters
		$charactersloop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $post_id, 'LIKE' );
		$queercount = $deadcount = $nonecount = $queerirlcount = 0;

		// Store as array to defeat some stupid with counting and prevent querying the database too many times
		if ($charactersloop->have_posts() ) {
			while ( $charactersloop->have_posts() ) {

				$charactersloop->the_post();
				$char_id     = get_the_ID();
				$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true);
				$is_dead     = has_term( 'dead', 'lez_cliches', $char_id);
				$is_none     = has_term( 'none', 'lez_cliches', $char_id);
				$is_queerirl = has_term( 'queer-irl', 'lez_cliches', $char_id);

				if ( $shows_array !== '' && get_post_status ( $char_id ) == 'publish' ) {
					foreach( $shows_array as $char_show ) {
						if ( $char_show['show'] == $post_id ) {
							$queercount++;
							if ( $is_dead == true )  $deadcount++;
							if ( $is_none == true )  $nonecount++;
							if ( $is_queerirl == true ) $queerirlcount++;
						}
					}
				}
			}
			wp_reset_query();
		}

		// Return Queers!
		if ( $type == 'count' )     return $queercount;
		if ( $type == 'dead' )      return $deadcount;
		if ( $type == 'none' )      return $nonecount;
		if ( $type == 'queer-irl' ) return $queerirlcount;
	}

	/**
	 * Calculate show character score.
	 */
	public static function show_character_score( $post_id, $type = '' ) {

		// Count characters
		$number_chars = self::count_queers( $post_id, 'count' );
		update_post_meta( $post_id, 'lezshows_char_count', $number_chars );
		
		// If there are no chars, the score will be zero, so bail early.
		if ( $number_chars == 0 ) return $number_chars;

		switch( $type ) {
			
			// Calculate the 'alive' score
			// Value of alive divided by total, multiplied by 100 
			case 'alive':
				// Count dead characters
				$number_dead = self::count_queers( $post_id, 'dead' );
				$score_alive = ( ( ( $number_chars - $number_dead ) / $number_chars ) * 100 );
				$score       = $score_alive;
				update_post_meta( $post_id, 'lezshows_dead_count', $number_dead );
				break;

			// Calculate the value of cliches
			// If everyone is queer IRL OR have no cliche, it's 100
			// Otherwise average the scores of queer IRL and no-cliches
			case 'cliches':
				$number_queerirl  = self::count_queers( $post_id, 'queer-irl' );
				$number_none      = self::count_queers( $post_id, 'none' );
				$score_characters = ( ( ( $number_queerirl + $number_none ) / $number_chars ) * 100 );
				$score            = $score_characters;
				break;
			default:
				$score = '';
		}

		return $score;
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

		// Get the ratings
		$score_show_rating  = self::show_score( $post_id );
		$score_chars_alive  = self::show_character_score( $post_id, 'alive' );
		$score_chars_cliche = self::show_character_score( $post_id, 'cliches' );

		// Calculate the full score, but don't go over 100
		$calculate = ( $score_show_rating + $score_chars_alive + $score_chars_cliche ) / 3;
		$the_score = min( $calculate, 100 );
		
		// Update the meta
		update_post_meta( $post_id, 'lezshows_the_score', $the_score );
	}

}

new LWTV_Shows_Calculate();
