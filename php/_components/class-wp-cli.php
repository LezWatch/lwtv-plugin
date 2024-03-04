<?php
/**
 * WP CLI.
 *
 * We have WP-CLI commands. We use them. Now they're organized.
 *
 * @since 2.0
 */

namespace LWTV\_Components;

class WP_CLI implements Component {

	public function init(): void {
		// Null
	}

	/**
	 * Constructor. Wraps around CLI.
	 */
	public function __construct() {
		if ( ! defined( 'WP_CLI' ) || false === WP_CLI ) {
			return;
		}

		// CLI Commands Loader.
		$cli_loader = array(
			sprintf( '%s/%s', dirname( __DIR__, 1 ) . '/wp-cli/', 'cli-calc.php' ),     // wp lwtv CALC [ID]
			sprintf( '%s/%s', dirname( __DIR__, 1 ) . '/wp-cli/', 'cli-check.php' ),    // wp lwtv CHECK [queerchars|wiki] [id]
			sprintf( '%s/%s', dirname( __DIR__, 1 ) . '/wp-cli/', 'cli-dupes.php' ),    // wp lwtv DUPES
			sprintf( '%s/%s', dirname( __DIR__, 1 ) . '/wp-cli/', 'cli-generate.php' ), // wp lwtv GENERATE [otd|tvmaze]
			sprintf( '%s/%s', dirname( __DIR__, 1 ) . '/wp-cli/', 'cli-shadow.php' ),   // wp lwtv SHADOW [post_type] [taxonomy]
		);

		foreach ( $cli_loader as $path_to_command ) {
			if ( file_exists( $path_to_command ) ) {
				require_once $path_to_command; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
			}
		}

		$args = array(
			'shortdesc' => 'Useful commands for LezWatch.TV.',
			'longdesc'  => 'Useful commands for LezWatch.TV.',
		);

		\WP_CLI::add_command( 'lwtv', 'WP_CLI', $args );
	}
}
