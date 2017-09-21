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
	public function show_score( $post_id ) {

		if ( !isset( $post_id ) ) return;

		// Get base ratings
		$realness   = min( (int) get_post_meta( $post_id, 'lezshows_realness_rating', true) , 5 );
		$quality    = min( (int) get_post_meta( $post_id, 'lezshows_quality_rating', true) , 5 );
		$screentime = min( (int) get_post_meta( $post_id, 'lezshows_screentime_rating', true) , 5 );

		// Base score
		$this_show = $realness + $quality + $screentime;

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

		// Add in Star Rating = -5, +1.5, +3, or +5
		switch ( get_post_meta( $post_id, 'lezshows_stars', true ) ) {
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

		// Trigger Warning = -5, -3, -1
		switch ( get_post_meta( $post_id, 'lezshows_triggerwarning', true ) ) {
			case "on":
				$this_show = $this_show - 5;
				break;
			case "med":
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

		// Shows We Love get a +5
		if ( get_post_meta( $post_id, 'lezshows_worthit_show_we_love', true ) == 'on' ) {
			$this_show = $this_show + 5;
		}

		// Calculate the score
		$max_score = 35;

		$score = ( $this_show / $max_score );

		return $score;
	}

	/*
	 * Count Queers
	 *
	 * This will update the metakey 'lezshows_char_count' on save
	 *
	 * @param int $post_id The post ID.
	 */
	public function count_queers( $post_id , $type = 'count' ) {

		// If this isn't a show post, return nothing.
		if ( get_post_type( $post_id ) !== 'post_type_shows' )
			return;

		// Loop to get the list of characters
		$charactersloop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $post_id, 'LIKE' );
		$queercount = $deadcount = $nonecount = 0;

		// Store as array to defeat some stupid with counting and prevent querying the database too many times
		if ($charactersloop->have_posts() ) {
			while ( $charactersloop->have_posts() ) {

				$charactersloop->the_post();
				$char_id     = get_the_ID();
				$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true);
				$is_dead     = has_term( 'dead', 'lez_cliches', $char_id);
				$is_none     = has_term( 'none', 'lez_cliches', $char_id);

				if ( $shows_array !== '' && get_post_status ( $char_id ) == 'publish' ) {
					foreach( $shows_array as $char_show ) {
						if ( $char_show['show'] == $post_id ) {
							$queercount++;
							if ( $is_dead == true ) $deadcount++;
							if ( $is_none == true ) $nonecount++;
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
	}

}

new LWTV_Shows_Calculate();