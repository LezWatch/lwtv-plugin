<?php
/**
 * Plugin Name: Core LezWatch.TV Plugin
 * Plugin URI:  https://lezwatchtv.com
 * Description: All the base code for LezWatch.TV - If this isn't active, the site dies. An ugly death.
 * Version: 6.0.1
 * Author: LezWatch.TV
 * Update URI: http://lezwatchtv.com
 * License: GPLv3
 *
 * @package LWTV
 */

/**
 * Copyright 2014-24 LezWatch.TV (webmaster@lezwatchtv.com)
 *
 * This file is part of the core LWTV plugin, a plugin for WordPress.
 *
 * Core LezWatch.TV Plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * Core LezWatch.TV Plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this installation as LICENSE.
 *
 * If not, see <https://www.gnu.org/licenses/gpl-3.0.html>.
 */

use LWTV\_Helpers\Autoload;
use LWTV\Plugin;

require_once __DIR__ . '/php/_helpers/class-autoload.php';

// Plugin Version.
define( 'LWTV_PLUGIN_VERSION', '6.0.1' );

// Define First Year with queers:
define( 'LWTV_FIRST_YEAR', '1961' );

// Define when the site started (Sept 2013):
define( 'LWTV_CREATED_YEAR', '2013' );

// Plugin Home:
define( 'LWTV_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Timezones:
define( 'LWTV_TIMEZONE', 'America/New_York' );
define( 'LWTV_SERVER_TIMEZONE', 'America/Los_Angeles' );

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
