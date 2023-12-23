<?php
/*
 * WP CLI Commands for LezWatch.TV
 *
 * These commands are 'check' tools.
 */

// Bail if directly accessed
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_CLI' ) ) {
	die();
}

/**
 * LezWatch.TV commands to check the sanctity of content.
 */
class WP_CLI_LWTV_Check {

	/**
	 * @var string
	 */
	public $format;

	/**
	 * @var string
	 */
	public $check;

	/**
	 * @var string
	 */
	public $second;

	/**
	 * @var bool
	 */
	public $fix_it = false;

	/**
	 * Construct to block facet from munging results.
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
	 * Check that all 'types' of a thing are okay.
	 *
	 * ## OPTIONS
	 *
	 * <check_name>
	 * : Type to check (i.e. 'queerchars').
	 *
	 * [<actor_id>]
	 * : Post ID to check
	 *
	 * [--fix-it]
	 * : Attempt to fix issues (not available for all checks).
	 * default: false
	 *
	 * ## EXAMPLES
	 *
	 * wp lwtv check queerchars
	 * wp lwtv check wiki [id]
	 * wp lwtv check isqueer [id]
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function __invoke( $args, $assoc_args = array() ) {

		$this->format = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );
		$this->fix_it = \WP_CLI\Utils\get_flag_value( $assoc_args, 'fix-it', null );
		$this->check  = $args[0];
		$this->second = isset( $args[1] ) ? $args[1] : '';

		try {
			$this->run_checker( $this->check, $this->second );
		} catch ( Exception $exception ) {
			\WP_CLI::error( $exception->getMessage(), false );
		}
	}

	/**
	 * Check what we've got.
	 */
	public function run_checker( $check_type, $second ) {
		$valid_types = array( 'queerchars', 'wiki', 'isqueer' );

		// Last sanity check: Is the post ID a member of THIS post type...
		if ( ! in_array( $check_type, $valid_types, true ) ) {
			$display_types = implode( ' or ', $valid_types );

			// Language check.
			if ( 3 >= count( $valid_types ) ) {
				$last          = array_pop( $valid_types );
				$display_types = implode( ', ', $valid_types ) . ' or ' . $last;
			}

			\WP_CLI::error( 'You can only run checks on ' . $display_types . '.' . $check_type . ' is invalid.' );
		}

		// Run the appropriate checker:
		switch ( $check_type ) {
			case 'queerchars':
				$this->run_queerchecker();
				break;
			case 'wiki':
				$this->run_wiki( $second );
				break;
			case 'isqueer':
				$this->run_isqueer( $second );
				break;
		}
	}

	/**
	 * Check wiki data.
	 *
	 * Currently only supports actors.
	 */
	public function run_wiki( $actor_id ) {
		$post_type = get_post_type( $actor_id );

		// Last sanity check: Is the post ID a member of THIS post type...
		if ( 'post_type_actors' !== $post_type ) {
			$real_post_type = rtrim( str_replace( 'post_type_', '', $post_type ), 's' );
			\WP_CLI::error( 'You are currently checking wikidata for actors, but ' . get_the_title( $actor_id ) . ' (#' . $actor_id . ') is a ' . $real_post_type . ', not an actor.' );
		}

		// Even though we only support actors...
		if ( 'post_type_actors' === $post_type ) {
			// Do the thing!
			$items        = lwtv_plugin()->check_actors_wikidata( $actor_id );
			$return_array = array( 'id', 'name', 'wikidata', 'birth', 'death', 'imdb', 'wikipedia', 'website', 'instagram', 'twitter', 'facebook' );

			if ( empty( $items ) ) {
				\WP_CLI::error( 'Something has gone horribly wrong. Go get Mika.' );
			} elseif ( ! isset( $items[ $actor_id ]['wikipedia'] ) ) {
				\WP_CLI::error( 'No data from WikiData.' );
			}
		}

		\WP_CLI::success( 'WikiData comparison for ' . get_the_title( $actor_id ) . ' complete!' );
		\WP_CLI\Utils\format_items( $this->format, $items, $return_array );
	}

	/**
	 * Check the queers.
	 */
	public function run_queerchecker() {
		$items = lwtv_plugin()->find_queer_chars();

		if ( ! isset( $items ) ) {
			\WP_CLI::error( 'An unexpected error has occurred. Go get Mika.' );
		} elseif ( empty( $items ) || ! is_array( $items ) ) {
			// Everything passed.
			\WP_CLI::success( 'Awesome! Check passes without any attention needed.' );
		} else {
			// These need attention
			\WP_CLI::log( count( $items ) . ' character(s) need your attention.' );
			\WP_CLI\Utils\format_items( $this->format, $items, array( 'url', 'id', 'problem' ) );
			\WP_CLI::success( 'Search complete.' );
		}
	}

	public function run_isqueer( $actor_id ) {
		$post_type = get_post_type( $actor_id );

		// Last sanity check: Is the post ID a member of THIS post type...
		if ( 'post_type_actors' !== $post_type ) {
			$real_post_type = rtrim( str_replace( 'post_type_', '', $post_type ), 's' );
			\WP_CLI::error( 'You are currently checking wikidata for actors, but ' . get_the_title( $actor_id ) . ' (#' . $actor_id . ') is a ' . $real_post_type . ', not an actor.' );
		}

		// Check 'em!
		$is_queer = lwtv_plugin()->is_actor_queer( $actor_id ) ? 'is queer' : 'is NOT queer';

		\WP_CLI::success( get_the_title( $actor_id ) . ' ' . $is_queer );
	}
}

\WP_CLI::add_command( 'lwtv check', 'WP_CLI_LWTV_Check' );
