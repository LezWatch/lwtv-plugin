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

		// These have to be called differently. Boo.
		require_once dirname( __DIR__, 1 ) . '/wp-cli/cli-calc.php';       // wp lwtv CALC [ID]
		require_once dirname( __DIR__, 1 ) . '/wp-cli/cli-check.php';      // wp lwtv CHECK [queerchars|wiki] [id]
		require_once dirname( __DIR__, 1 ) . '/wp-cli/cli-generate.php';   // wp lwtv GENERATE [otd|tvmaze]
	}
}
