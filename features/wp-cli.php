<?php
/*
 * WP CLI Commands for LezWatch.TV
 *
 * @since 2.0
 */

// Bail if directly accessed
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_CLI' ) ) {
	die();
}

/**
 * LezWatch.TV special commands
 *
 * ## EXAMPLES
 *
 * Re-run calculations for specific post content.
 * $ wp lwtv calc [show|actor] [ID]
 *
 * Find post content missing certain flags
 * $ wp lwtv find queerchars
 *
 */
class WP_CLI_LWTV_Commands extends WP_CLI_Command {

	public function __construct() {
		// phpcs:disable
		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
			return false;
		}, 10, 2 );
		// phpcs:enable
	}

	/**
	 * Re-run calculations for specific post content.
	 *
	 * ## EXAMPLES
	 *
	 *    wp lwtv calc actor ID
	 *    wp lwtv calc show ID
	 *
	*/
	public function calc( $args, $assoc_args ) {

		// Valid things to calculate:
		$valid_calcs = array( 'actor', 'show' );

		// Defaults
		$format = ( isset( $assoc_args['format'] ) ) ? $assoc_args['format'] : 'table';

		// Check for valid arguments and post types
		if ( empty( $args ) || ! in_array( $args[0], $valid_calcs, true ) ) {
			WP_CLI::error( 'You must provide a valid type of calculation to run: ' . implode( ', ', $valid_calcs ) );
		}

		// Check for valid IDs
		if ( empty( $args[1] ) || ! is_numeric( $args[1] ) ) {
			WP_CLI::error( 'You must provide a valid post ID to calculate.' );
		}

		// Set the post IDs:
		$post_calc = sanitize_text_field( $args[0] );
		$post_id   = (int) $args[1];

		// Last sanitity check: Is the post ID a member of THIS post type...
		if ( get_post_type( $post_id ) !== 'post_type_' . $post_calc . 's' ) {
			WP_CLI::error( 'You can only calculate ' . $post_type . 's on ' . $post_type . ' pages.' );
		}

		// Do the thing!
		// i.e. run the calculations
		switch ( $post_calc ) {
			case 'show':
				// Rerun show calculations
				LWTV_Shows_Calculate::do_the_math( $post_id );
				$chars = get_post_meta( $post_id, 'lezshows_char_count', true );
				$dead  = get_post_meta( $post_id, 'lezshows_dead_count', true );
				$score = 'Score (' . get_post_meta( $post_id, 'lezshows_the_score', true ) . ') Chars (' . $chars . ') Dead (' . $dead . ')';
				break;
			case 'actor':
				// Recount characters and flag queerness
				LWTV_Actors_Calculate::do_the_math( $post_id );
				$queer = ( get_post_meta( $post_id, 'lezactors_queer', true ) ) ? 'Yes' : 'No';
				$chars = get_post_meta( $post_id, 'lezactors_char_count', true );
				$deads = get_post_meta( $post_id, 'lezactors_dead_count', true );
				$score = 'Is Queer (' . $queer . ') Chars (' . $chars . ') Dead (' . $deads . ')';
				break;
		}

		WP_CLI::success( 'Calculations run for ' . get_the_title( $post_id ) . ': ' . $score );
	}

	/**
	 * Get information on WikiData
	 *
	 * ## EXAMPLES
	 *
	 *    wp lwtv wiki actor ID
	 *
	*/
	public function wiki( $args, $assoc_args ) {

		// Valid things to calculate:
		$valid_wiki = array( 'actor' );

		// Defaults
		$format = ( isset( $assoc_args['format'] ) ) ? $assoc_args['format'] : 'table';

		// Check for valid arguments and post types
		if ( empty( $args ) || ! in_array( $args[0], $valid_wiki, true ) ) {
			WP_CLI::error( 'You must provide a valid type of content to check against WikiData. Currently we can only check actors.' );
		}

		// Check for valid IDs
		if ( empty( $args[1] ) || ! is_numeric( $args[1] ) ) {
			WP_CLI::error( 'You must provide a valid post ID to calculate.' );
		}

		// Set the post IDs:
		$post_type = sanitize_text_field( $args[0] );
		$post_id   = (int) $args[1];

		// Last sanity check: Is the post ID a member of THIS post type...
		if ( get_post_type( $post_id ) !== 'post_type_' . $post_type . 's' ) {
			WP_CLI::error( 'You can only check data for ' . $post_type . 's on ' . $post_type . ' pages.' );
		}

		// Do the thing!
		// i.e. run the calculations
		switch ( $post_type ) {
			case 'actor':
				$items = LWTV_Debug::check_actors_wikidata( $post_id );
				break;
		}

		if ( empty( $items ) ) {
			WP_CLI::error( 'Something has gone horribly wrong. Go get Mika.' );
		} elseif ( ! isset( $items[ $post_id ]['wikipedia'] ) ) {
			WP_CLI::error( 'No data from WikiData.' );
		}

		WP_CLI::success( 'WikiData comparison for ' . get_the_title( $post_id ) . ' complete!' );
		WP_CLI\Utils\format_items( $format, $items, array( 'id', 'name', 'birth', 'death', 'imdb', 'wikipedia', 'instagram', 'twitter', 'website' ) );
	}

	/**
	 * Find post content missing certain flags.
	 *
	 * ## EXAMPLES
	 *
	 *    wp lwtv find queerchars
	 *
	*/
	public function find( $args, $assoc_args ) {

		// Valid things to find...
		$valid_finds = array( 'queerchars' );
		$format      = ( isset( $assoc_args['format'] ) ) ? $assoc_args['format'] : 'table';
		$try_to_fix  = false;

		// What are we finding?
		if ( ! empty( $args ) ) {
			list( $find ) = $args;
		}

		// Check for valid arguments and post types
		if ( empty( $find ) || ! in_array( $find, $valid_finds, true ) ) {
			WP_CLI::error( 'You must provide a valid type of item to find: ' . implode( ', ', $valid_finds ) );
		}

		// If the fix flag is flown, try to fix.
		if ( WP_CLI\Utils\get_flag_value( $assoc_args, 'fix' ) ) {
			$try_to_fix = true;
		}

		switch ( $find ) {
			case 'queerchars':
				WP_CLI::log( 'Searching all characters for associated actor queerness ...' );
				$items = LWTV_Debug::find_queerchars();
				break;
			case 'nochars':
				if ( $try_to_fix ) {
					WP_CLI::log( 'Attempting to fix actors without characters ....' );
					$items = LWTV_Debug::fix_actors_no_chars();
				} else {
					WP_CLI::log( 'Searching all actors to ensure they have a character ....' );
					$items = LWTV_Debug::find_actors_no_chars();
				}
		}

		if ( empty( $items ) || ! is_array( $items ) ) {
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
