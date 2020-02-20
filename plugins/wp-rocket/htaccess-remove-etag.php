<?php
/**
 * Plugin Name: WP Rocket | Remove ETag
 * Description: Prevents ETag code to be added to htaccess.
 * Plugin URI:  https://github.com/wp-media/wp-rocket-helpers/tree/master/htaccess/wp-rocket-htaccess-remove-etag/
 * Author:      WP Rocket Support Team
 * Author URI:  http://wp-rocket.me/
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright SAS WP MEDIA 2018
 */

// Namespaces must be declared before any other declaration.
namespace WP_Rocket\Helpers\htaccess\remove_etag;

// Standard plugin security, keep this line in place.
defined( 'ABSPATH' ) or die();

/**
 * Prevents ETag code to be added to htaccess.
 */
add_filter ( 'rocket_htaccess_etag', '__return_false');

/**
 * Updates .htaccess, regenerates WP Rocket config file.
 *
 * @author Caspar Hübinger
 */
function flush_wp_rocket() {

	if ( ! function_exists( 'flush_rocket_htaccess' )
	  || ! function_exists( 'rocket_generate_config_file' ) ) {
		return false;
	}

	// Update WP Rocket .htaccess rules.
	flush_rocket_htaccess();

	// Regenerate WP Rocket config file.
	rocket_generate_config_file();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\flush_wp_rocket' );

/**
 * Removes customizations, updates .htaccess, regenerates config file.
 *
 * @author Caspar Hübinger
 */
function deactivate() {

	// Remove all functionality added above.
	remove_filter ( 'rocket_htaccess_etag', '__return_false');

	// Flush .htaccess rules, and regenerate WP Rocket config file.
	flush_wp_rocket();
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate' );
