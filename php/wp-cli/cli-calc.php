<?php
/*
 * WP CLI Commands for LezWatch.TV
 *
 * These commands are 'calculation' tools.
 */

// Bail if directly accessed
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_CLI' ) ) {
	die();
}

/**
 * LezWatch.TV commands to calculate data.
 */
class WP_CLI_LWTV_Calculate {

	/**
	 * @var string
	 */
	public $format;

	/**
	 * @var int
	 */
	public $post_id;

	/**
	 * Construct to obviate facet from munging results.
	 */
	public function __construct() {
		// phpcs:disable
		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
			return false;
		}, 10, 2 );
		// phpcs:enable
	}

	/**
	 * Re-run calculations for scores and character counts.
	 *
	 * ## OPTIONS
	 *
	 * <post_id>
	 * : Post ID to calculate.
	 *
	 * wp lwtv calc 1234
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function __invoke( $args, $assoc_args = array() ) {

		$this->format  = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );
		$this->post_id = $args[0];

		try {
			$this->run_calculations( $this->post_id );
		} catch ( Exception $exception ) {
			\WP_CLI::error( $exception->getMessage(), false );
		}
	}

	/**
	 * Re-run calculations
	 *
	 * @param int    $post_id    Post ID.
	 */
	public function run_calculations( $post_id ) {

		// Bail ASAP if the post ID is invalid.
		if ( false === get_post_status( $post_id ) ) {
			\WP_CLI::error( $post_id . ' is not a valid post.' );
		}

		$valid_types = array( 'actor', 'show' );
		$post_type   = rtrim( str_replace( 'post_type_', '', get_post_type( $post_id ) ), 's' );

		// Last sanity check: Is the post ID a member of THIS post type...
		if ( ! in_array( $post_type, $valid_types, true ) ) {
			$display_types = implode( ' or ', $valid_types );
			if ( 3 >= count( $valid_types ) ) {
				$last          = array_pop( $valid_types );
				$display_types = implode( ', ', $valid_types ) . ' or ' . $last;
			}
			\WP_CLI::error( 'You can only run calculations on ' . $display_types . ' post types, but ' . get_the_title( $post_id ) . ' (#' . $post_id . ') is a ' . $post_type . '.' );
		}

		// Switch to run the commands since they're different.
		switch ( $post_type ) {
			case 'actor':
				// Recount characters and flag queerness
				delete_post_meta( $post_id, 'lezactors_char_count' );
				delete_post_meta( $post_id, 'lezactors_dead_count' );
				lwtv_plugin()->calculate_actor_data( $post_id );
				$queer = ( get_post_meta( $post_id, 'lezactors_queer', true ) ) ? 'Yes' : 'No';
				$chars = get_post_meta( $post_id, 'lezactors_char_count', true );
				$deads = get_post_meta( $post_id, 'lezactors_dead_count', true );
				$score = 'Is Queer (' . $queer . ') Chars (' . $chars . ') Dead (' . $deads . ')';
				break;
			case 'show':
				delete_post_meta( $post_id, 'lezshows_char_count' );
				delete_post_meta( $post_id, 'lezshows_dead_count' );
				lwtv_plugin()->calculate_show_data( $post_id );
				$chars = get_post_meta( $post_id, 'lezshows_char_count', true );
				$dead  = get_post_meta( $post_id, 'lezshows_dead_count', true );
				$score = 'Score (' . get_post_meta( $post_id, 'lezshows_the_score', true ) . ') Chars (' . $chars . ') Dead (' . $dead . ')';
				break;
		}

		\WP_CLI::success( 'Calculations run for ' . get_the_title( $post_id ) . ': ' . $score );
	}
}

\WP_CLI::add_command( 'lwtv calc', 'WP_CLI_LWTV_Calculate', $args );
