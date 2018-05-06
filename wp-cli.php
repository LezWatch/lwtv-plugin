<?php
/*
 * WP CLI Commands for LezWatchTV
 *
 * @since 2.0
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
	 * Re-run calculations for specific post content.
	 * 
	 * ## EXAMPLES
	 * 
	 *		wp lwtv calc actor ID
	 *		wp lwtv calc show ID
	 *
	*/
	
	function calc( $args , $assoc_args ) {

		// Valid things to calculate:
		$valid_calcs = array( 'actor', 'show' );
		
		// Defaults
		$format = ( isset( $assoc_args['format'] ) )? $assoc_args['format'] : 'table';

		// Check for valid arguments and post types
		if ( empty( $args ) || !in_array( $args[0], $valid_calcs ) ) {
			WP_CLI::error( 'You must provide a valid type of calculation to run: ' . implode( ', ', $valid_calcs ) );
		}

		// Check for valid IDs
		if( empty( $args[1] ) || !is_numeric( $args[1] ) ) {
			WP_CLI::error( 'You must provide a valid post ID to calculate.' );
		}

		// Set the post IDs:
		$post_calc = sanitize_text_field( $args[0] );
		$post_id   = (int)$args[1];

		// Last sanitity check: Is the post ID a member of THIS post type...
		if ( get_post_type( $post_id ) !== 'post_type_' . $post_calc . 's' ) {
			WP_CLI::error( 'You can only calculate ' . $post_type . 's on ' . $post_type . ' pages.' );
		}

		// Do the thing!
		// i.e. run the calculations
		switch( $post_calc ) {
			case 'show':
				// Rerun show calculations
				LWTV_Shows_Calculate::do_the_math( $post_id );
				$score = 'Score: ' . get_post_meta( $post_id, 'lezshows_the_score', true );
				break;
			case 'actor':
				// Recount characters and flag queerness
				LWTV_Actors_Calculate::do_the_math( $post_id );
				$queer = ( get_post_meta( $post_id, 'lezactors_queer', true ) )? 'Yes' : 'No';
				$chars = get_post_meta( $post_id, 'lezactors_char_count', true );
				$deads = get_post_meta( $post_id, 'lezactors_dead_count', true );
				$score = ': Is Queer (' . $queer . ') Chars (' . $chars . ') Dead (' . $deads . ')';
				break;
		}

		WP_CLI::success( 'Calculations run for ' . get_the_title( $post_id ) . $score );
	}

	/**
	 * Find post content missing certain flags.
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
			WP_CLI::error( 'Currently you can only use the "queerchars" param to find characters played by queer actors, missing the appropriate flags.' );
		}

		WP_CLI::log( 'Searching all characters for associated actor queerness. This may take a while ....' );

		$items = LWTV_Debug::find_queerchars();

		if ( empty( $items ) || !is_array( $items ) ) {
			// No one needs help
			WP_CLI::success( 'Awesome! Everyone\'s great!' );
		} else {
			// These characters need attention
			WP_CLI\Utils\format_items( $format, $items, array( 'url', 'id', 'problem' ) );
			WP_CLI::log( count( $items ) . ' character(s) need your attention.' );
		}

		WP_CLI::success( 'Search complete.' );
	}

}

WP_CLI::add_command( 'lwtv', 'WP_CLI_LWTV_Commands' );