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

	public function __construct() {
		if ( ! defined( 'WP_CLI' ) || false === WP_CLI ) {
			return;
		}

		// These have to be called differently. Boo.
		require_once dirname( __DIR__, 1 ) . 'cli-calc.php';       // wp lwtv CALC [ID]
		require_once dirname( __DIR__, 1 ) . 'cli-check.php';      // wp lwtv CHECK [queerchars|wiki] [id]
		require_once dirname( __DIR__, 1 ) . 'cli-generate.php';   // wp lwtv GENERATE [otd|tvmaze]
	}
}
