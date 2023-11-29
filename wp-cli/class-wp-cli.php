<?php
/**
 * WP CLI.
 *
 * We have WP-CLI commands. We use them. Now they're organized.
 *
 * @since 2.0
 */

class LWTV_WP_CLI {

	public function __construct() {
		if ( ! defined( 'WP_CLI' ) || false === WP_CLI ) {
			return;
		}

		// These have to be called differently. Boo.
		require_once 'cli-calc.php';       // wp lwtv CALC [ID]
		require_once 'cli-check.php';      // wp lwtv CHECK [queerchars|wiki] [id]
		require_once 'cli-generate.php';   // wp lwtv GENERATE [otd|tvmaze]
	}
}
