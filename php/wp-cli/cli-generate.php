<?php
/*
 * WP CLI Commands for LezWatch.TV
 *
 * These commands are 'generation' tools.
 */

// Bail if directly accessed
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_CLI' ) ) {
	die();
}

/**
 * LezWatch.TV commands to regenerate content.
 */
class WP_CLI_LWTV_Generate {

	/**
	 * @var string
	 */
	public $format;

	/**
	 * @var string
	 */
	public $gen_type;

	/**
	 * @var string
	 */
	public $second;

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
	 * Generate files or abnormal code settings.
	 *
	 * ## OPTIONS
	 *
	 * <type>
	 * : Type to content to generate (i.e. 'TVmaze').
	 *
	 * ## EXAMPLES
	 *
	 * wp lwtv generate tvmaze
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function __invoke( $args, $assoc_args = array() ) {

		$this->format   = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );
		$this->gen_type = $args[0];
		$this->second   = ( isset( $args[1] ) ) ? $args[1] : null;

		try {
			$this->run_generator( $this->gen_type, $this->second );
		} catch ( Exception $exception ) {
			\WP_CLI::error( $exception->getMessage(), false );
		}
	}

	/**
	 * Build it!
	 *
	 * @param string $type   Type of content to generate
	 * @param string $second Secondary data (may not be used)
	 */
	public function run_generator( $type, $second ) {
		// Run the appropriate checker:
		switch ( $type ) {
			case 'tvmaze':
				$buildit = $this->run_tvmaze();
				break;
			case 'otd':
				$buildit = $this->run_otd( $second );
				break;
			default:
				$buildit = false;
		}

		if ( false === $buildit ) {
			\WP_CLI::error( 'You picked an invalid tool to generate. ' . $type . ' does not exist.' );
		}
	}

	/**
	 * Regenerate the TV Maze ICS file.
	 */
	public function run_tvmaze() {
		$upload_dir = wp_upload_dir();
		$ics_file   = $upload_dir['basedir'] . '/tvmaze.ics';
		$response   = wp_remote_get( TV_MAZE );
		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents( $ics_file, $response['body'] );
			\WP_CLI::success( 'TVMaze updated successfully.' );
		} else {
			\WP_CLI::error( 'TVMaze is not able to be updated.' );
		}
	}

	/**
	 * Set "Of the Day" for the day.
	 *
	 * @param array $otd Which 'of the day' are we making.
	 */
	public function run_otd( $otd ) {
		// Valid things to find...
		$valid_otd = array( 'character', 'show' );

		// Check for valid arguments and post types
		if ( ! empty( $otd ) && ! in_array( $otd, $valid_otd, true ) ) {
			\WP_CLI::error( 'You must provide a valid type of item to set for "Of the Day": ' . implode( ', ', $valid_otd ) );
		}

		if ( empty( $otd ) ) {
			$to_do = $valid_otd;
		} else {
			$to_do = array( $otd );
		}

		// Set it!
		foreach ( $to_do as $otd ) {
			lwtv_plugin()->set_of_the_day( $otd );
			\WP_CLI::success( 'The ' . $otd . ' "Of the Day" has been set.' );
		}
	}
}

\WP_CLI::add_command( 'lwtv generate', 'WP_CLI_LWTV_Generate' );
