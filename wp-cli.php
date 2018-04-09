<?php
/**
	WP CLI Commands for LezWatchTV
	Copyright 2017-2018 Mika Epstein (email: ipstenu@halfelf.org)
*/

// Bail if directly accessed
if ( !defined( 'ABSPATH' ) ) die();

// Bail if WP-CLI is not present
if ( !defined( 'WP_CLI' ) ) return;

/**
 * LezWatch special commands
 */
class WP_CLI_LWTV_Commands extends WP_CLI_Command {

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
	 * Find characters missing certain flag
	 * Currently just to help find 
	 * 
	 * ## EXAMPLES
	 * 
	 *		wp lwtv find queerchars
	 *
	*/
	
	function find( $args , $assoc_args ) {

		// What are we looking for?
		if ( !empty($args) ) list( $find ) = $args;

		// Valid things to find...
		$valid_find = array( 'queerchars' );
		$format     = ( isset( $assoc_args['format'] ) )? $assoc_args['format'] : 'table';

		if ( !in_array( $find, $valid_find ) ) {
			WP_CLI::error( 'Currently you can only use the "queerchars" param to find character played by queer actors, missing the appropriate flags.' );
		}

		WP_CLI::log( 'This may take a while ....' );

		// Get all the characters
		$the_loop = LWTV_Loops::post_type_query( 'post_type_characters' );

		if ( $the_loop->have_posts() ) {
			while ( $the_loop->have_posts() ) {
				$the_loop->the_post();
				$post = get_post();

				// Get the actors...
				$character_actors = get_post_meta( $post->ID, 'lezchars_actor', true );

				if( !$character_actors || empty( $character_actors ) ) {
					// If there are no actors, we have a different problem...
					$items[] = array( 'url' => get_permalink(),  'problem' => 'No actors... Ooops.' );
				} else {

					// Get the defaults
					$flagged_queer = ( has_term( 'queer-irl', 'lez_cliches' ) )? true : false;
					$actor_queer   = false;

					if ( !is_array ( $character_actors ) ) {
						$character_actors = array( get_post_meta( $post->ID, 'lezchars_actor', true ) );
					}

					// If ANY actor is flagged as queer, we're queer.
					foreach ( $character_actors as $actor ) {
						$actor_queer = ( LWTV_Loops::is_actor_queer( $actor ) == 'yes' || $actor_queer )? true : false;
					}
					
					if ( $actor_queer && !$flagged_queer ) {
						$items[] = array( 'url' => get_permalink(), 'problem' => 'Missing Queer IRL tag' );
					}

					if ( !$actor_queer && $flagged_queer ) {
						$items[] = array( 'url' => get_permalink(),  'problem' => 'No actor is queer' );
					}
				}
			}
			wp_reset_query();
		}

		if ( empty( $items ) || !is_array( $item ) ) {
			// No one needs help
			WP_CLI::success( 'Awesome! Everyone\'s great!' );
		} else {
			// These characters need attention
			WP_CLI\Utils\format_items( $format, $items, array( 'url', 'problem' ) );
			WP_CLI::success( count( $items ) . ' character(s) need your attention.' );
		}
	}

}

WP_CLI::add_command( 'lwtv', 'WP_CLI_LWTV_Commands' );