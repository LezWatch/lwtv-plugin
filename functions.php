<?php
/**
 * Plugin Name: Core LezWatch.TV Plugin
 * Plugin URI:  https://lezwatchtv.com
 * Description: All the base code for LezWatch.TV - If this isn't active, the site dies. An ugly death.
 * Version: 6.0
 * Author: LezWatch.TV
 * Update URI: http://lezwatchtv.com
 *
 * @package LWTV
 */

use LWTV\_Helpers\Autoload;
use LWTV\Plugin;

require_once __DIR__ . '/php/_helpers/class-autoload.php';

// Plugin Version
define( 'LWTV_PLUGIN_VERSION', '6.0' );

// Define First Year:
define( 'LWTV_FIRST_YEAR', '1961' );

// Plugin Home
define( 'LWTV_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Symbolicons
 */
$upload_dir = wp_upload_dir();
define( 'LWTV_SYMBOLICONS_PATH', $upload_dir['basedir'] . '/lezpress-icons/symbolicons/' );
define( 'LWTV_SYMBOLICONS_URL', $upload_dir['baseurl'] . '/lezpress-icons/symbolicons/' );

/**
 * Autoloader serves for `LWTV` namespace and autoload all files under the php directory.
 *
 * To add a new component, see the file /php/class-plugin.php
 */
$autoload = new Autoload();
$autoload->add( 'LWTV', sprintf( '%s/php', __DIR__ ) );

/**
 * Retrieves an instance of the Plugin.
 *
 * @return Plugin
 */
function lwtv_plugin() {
	static $plugin = null;

	if ( ! $plugin ) {
		$plugin = new Plugin();
		$plugin->init();
	}

	return $plugin;
}

lwtv_plugin();
