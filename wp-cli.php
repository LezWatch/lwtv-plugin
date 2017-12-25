<?php
/**
	Copyright 2017 Mika Epstein (email: ipstenu@halfelf.org)
*/

if ( !defined( 'ABSPATH' ) ) {
    die();
}

// Bail if WP-CLI is not present
if ( !defined( 'WP_CLI' ) ) return;

/**
 * Calculate Show Score
 */
class WP_CLI_LWTV_ShowCalc_Command extends WP_CLI_Command {

	public function __construct() {
		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );
	}
	
	/**
	 * Show calculation
	 * 
	 * ## EXAMPLES
	 * 
	 *		wp lwtv showcalc ID
	 *
	*/
	
	function showcalc( $args , $assoc_args ) {

		// Set the post ID
		if ( !empty($args) ) { list( $post_id ) = $args; }

		// If the post_id is not empty, make sure it's legit
		if ( !empty( $post_id ) ) {
			if ( get_post_type( $post_id ) == 'post_type_shows' ) {
				LWTV_Shows_Calculate::do_the_math( $post_id );
				$score = get_post_meta( $post_id, 'lezshows_the_score', true );
			} else {
				WP_CLI::error( 'You can only calculate the score on show pages.' );
			}
		} else {
			WP_CLI::error( 'You can only calculate the score on show pages.' );
		}
		
		WP_CLI::success( 'Score reset for ' . get_the_title( $post_id ) . ': ' . $score );
	}

	/**
	 * Runs various LWTV related scripts
	 * 
	 * ## EXAMPLES
	 * 
	 *		wp lwtv actormeta ID
	 *
	*/
	
	function actormeta( $args , $assoc_args ) {

		// Set the post ID
		if ( !empty($args) ) { list( $post_id ) = $args; }

		// If the post_id is not empty, make sure it's legit
		if ( !empty( $post_id ) ) {
			if ( get_post_type( $post_id ) == 'post_type_actors' ) {

				$number_chars = count( lwtv_yikes_actordata( $post_id, 'characters' ) );
				$number_dead  = count( lwtv_yikes_actordata( $post_id, 'dead' ) );
				$is_queer     = ( LWTV_Loops::is_actor_queer( $post_id ) == 'yes' )? true : false;
				update_post_meta( $post_id, 'lezactors_char_count', $number_chars );
				update_post_meta( $post_id, 'lezactors_dead_count', $number_dead );
				update_post_meta( $post_id, 'lezactors_queer', $is_queer );
				
				$output  = $number_chars . ' chars, ' . $number_dead . ' dead';
				$output .= ( $is_queer )? ', is queer' : '';

				// If we're not queer, let's check if they need to be updated to have SOME data ...
				if ( !$is_queer ) {
					$gender_terms    = get_the_terms( $post_id, 'lez_actor_gender', true );
					$sexuality_terms = get_the_terms( $post_id, 'lez_actor_sexuality', true );

					// If no gender is set OR there's an error...
					if ( !$gender_terms || is_wp_error( $gender_terms ) ) {
						wp_set_object_terms( $post_id, 'cis-woman', 'lez_actor_gender', false );
						$output .= ', added cis-woman';
					}
					// Ditto Gender
					if ( !$sexuality_terms || is_wp_error( $sexuality_terms ) ) {
						wp_set_object_terms( $post_id, 'heterosexual', 'lez_actor_sexuality', false );
						$output .= ', added heterosexual';
					}
				}

			} else {
				WP_CLI::error( 'You can only update actor meta on actor pages.' );
			}
		} else {
			WP_CLI::error( 'You can only update actor meta on actor pages.' );
		}
		
		WP_CLI::success( 'Meta data updated for ' . get_the_title( $post_id ) . ': ' . $output );
	}

}

WP_CLI::add_command( 'lwtv', 'WP_CLI_LWTV_ShowCalc_Command' );