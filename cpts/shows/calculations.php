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
		$realness   = min( (int) get_post_meta( $post_id, 'lezshows_realness_rating', true) , 5 );
		$quality    = min( (int) get_post_meta( $post_id, 'lezshows_quality_rating', true) , 5 );
		$screentime = min( (int) get_post_meta( $post_id, 'lezshows_screentime_rating', true) , 5 );
		$this_show  = $realness + $quality + $screentime;
		$max_score  = 15;
		
		// Add in Thumb Score Rating = +5 or -5
		switch ( get_post_meta( $post_id, 'lezshows_worthit_rating', true ) ) {
			case "Yes":
				$this_show = $this_show + 5;
				break;
			case "No":
				$this_show = $this_show - 5;
				break;
			default:
				$this_show = $this_show;
		}
		$max_score  = $max_score + 5;

		// Add in Star Rating = -5, +1.5, +3, or +5
		$star_terms = get_the_terms( $post_id, 'lez_stars' );
		$color = ( !empty( $star_terms ) && !is_wp_error( $star_terms ) )? $star_terms[0]->slug : get_post_meta( $post_id, 'lez_stars', true );
		switch ( $color ) {
			case "gold":
				$this_show = $this_show + 5;
				break;
			case "silver":
				$this_show = $this_show + 3;
				break;
			case "bronze":
				$this_show = $this_show + 1.5;
				break;
			case "anti":
				$this_show = $this_show - 5;
				break;
			default:
				$this_show = $this_show;
		}
		$max_score  = $max_score + 5;

		// Trigger Warning = -5, -3, -1
		$trigger_terms = get_the_terms( $post_id, 'lez_triggers' );
		$trigger = ( !empty( $trigger_terms ) && !is_wp_error( $trigger_terms ) )? $trigger_terms[0]->slug : get_post_meta( $post_id, 'lezshows_triggerwarning', true );
		switch ( $trigger ) {
			case "on":
			case "high":
				$this_show = $this_show - 5;
				break;
			case "med":
			case "medium":
				$this_show = $this_show - 3;
				break;
			case "low":
				$this_show = $this_show - 1;
				break;
			default:
				$this_show = $this_show;
		}

		// No Tropes = +5
		if ( has_term( 'none', 'lez_tropes', $post_id ) ) {
			$this_show = $this_show + 5;
		}
		$max_score  = $max_score + 5;

		// Shows We Love get a +5
		if ( get_post_meta( $post_id, 'lezshows_worthit_show_we_love', true ) == 'on' ) {
			$this_show = $this_show + 5;
		}
		$max_score  = $max_score + 5;

		// Calculate the score
		$score = $this_show + ( 100 - $max_score );

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
		if ( $type == 'count' ) return $queercount;
		if ( $type == 'dead' ) return $deadcount;
		if ( $type == 'none' ) return $nonecount;
		if ( $type == 'queer-irl' ) return $queerirlcount;
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
		// Count characters
		$number_chars = self::count_queers( $post_id, 'count' );
		update_post_meta( $post_id, 'lezshows_char_count', $number_chars );

		// Count dead characters
		$number_dead = self::count_queers( $post_id, 'dead' );
		update_post_meta( $post_id, 'lezshows_dead_count', $number_dead );

		// Count 'no cliche' characters
		$number_none = self::count_queers( $post_id, 'none' );

		// Count 'queer irl' characters
		$number_queerirl = self::count_queers( $post_id, 'queer-irl' );

		// Calculate alive characters
		if ( $number_dead == 0 && $number_chars > 0 ) {
			$score_alive = 100;
		} elseif ( $number_chars == 0 ) {
			$score_alive = 0; 
		} elseif ( $number_dead == $number_chars ) {
			$score_alive = 100;
		}
		else {
			$score_alive = ( ( ( $number_chars - $number_dead ) / $number_chars ) * 100 );
		}

		// Calculate queer IRL characters
		if ( $number_chars == 0 || $number_queerirl == 0 ) {
			$score_queer_irl = 0;
		} else {
			$score_queer_irl = ( ( $number_queerirl / $number_chars ) * 100 );
		}

		// Calculate cliche free characters
		if ( $number_chars == 0 || $number_none == 0 ) {
			$score_no_cliche = 0;
		} else {
			$score_no_cliche = ( ( $number_none / $number_chars ) * 100 );
		}

		// Calculate character score
		if ( $score_no_cliche == 0 && $score_queer_irl == 0 ) {
			$score_characters = 0;
		} elseif ( $score_no_cliche == 100 || $score_queer_irl == 100 ) {
			$score_characters = 100;
		} else {
			$score_characters = ( ( $score_no_cliche + $score_queer_irl ) / 2 );
		}

		// Calculate the ratings value
		$score_show_rating = self::show_score( $post_id );

		// Calculate the full score
		$calculate = ( $score_show_rating + $score_alive + $score_characters ) / 3;
		$the_score = min( $calculate, 100 );
		
		update_post_meta( $post_id, 'lezshows_the_score', $the_score );
	}

}

new LWTV_Shows_Calculate();