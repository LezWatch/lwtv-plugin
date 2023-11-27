<?php
/**
 * Plugin Name: Core LezWatch.TV Plugin
 * Plugin URI:  https://lezwatchtv.com
 * Description: All the base code for LezWatch.TV - If this isn't active, the site dies. An ugly death.
 * Version: 5.0
 * Author: LezWatch.TV
 * Update URI: http://lezwatchtv.com
 *
 * @package LWTV
 */

/**
 * Set up Autoloader.
 *
 * @package Galvanized_Network_Plugin
 */

use LWTV\Helpers\Autoload;
use LWTV\Helpers\Components;

// Call Autoloader
require_once 'helpers/class-autoload.php';
require_once 'helpers/class-components.php';
require_once 'helpers/defines.php';

$components      = new Components();
$core_components = $components->core_components();

$autoload = new Autoload();

// Load core components.
foreach ( $core_components as $component ) {
	$autoload->add( $component );
}
