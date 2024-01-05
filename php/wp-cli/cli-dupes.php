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
class WP_CLI_LWTV_Dupes {

	/**
	 * @var string
	 */
	public $format;

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
	 * Find all potential Duplicates.
	 *
	 * ## EXAMPLES
	 *
	 * wp lwtv dupes
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function __invoke( $args, $assoc_args = array() ) {

		$this->format = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );

		try {
			$this->find_dupes();
		} catch ( Exception $exception ) {
			\WP_CLI::error( $exception->getMessage(), false );
		}
	}

	public function find_dupes() {
		$items = lwtv_plugin()->find_duplicates();

		if ( empty( $items ) ) {
			\WP_CLI::success( 'Woohoo! No duplicates!' );
		}

		\WP_CLI::success( 'We have found the following duplicates' );
		\WP_CLI\Utils\format_items( $this->format, $items, array( 'id', 'name', 'problem' ) );
	}
}

\WP_CLI::add_command( 'lwtv dupes', 'WP_CLI_LWTV_Dupes' );
