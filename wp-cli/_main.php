<?php
/**
 * WP CLI.
 *
 * We have WP-CLI commands. We use them. Now they're organized.
 *
 * @since 2.0
 */

/**
 * Only include this if WP-CLI is being used.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once 'calc.php';       // wp lwtv CALC [ID]
	require_once 'check.php';      // wp lwtv CHECK [queerchars|wiki] [id]
	require_once 'generate.php';   // wp lwtv GENERATE [otd|tvmaze]
}
