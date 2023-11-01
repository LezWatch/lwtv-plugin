<?php
/**
 * Name: Customization for Plugins
 *
 */

// Include the base files
require_once 'cache.php';
require_once 'gutenslam.php';

// Include conditionally if the parent is active.
if ( is_plugin_active( 'cmb2/init.php' ) ) {
	require_once 'cmb2.php';
}
if ( is_plugin_active( 'facetwp/index.php' ) ) {
	require_once 'facetwp.php';
}
if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
	require_once 'gravity-forms.php';
}
if ( is_plugin_active( 'jetpack/jetpack.php' ) ) {
	require_once 'jetpack.php';
}
if ( is_plugin_active( 'related-posts-by-taxonomy/related-posts-by-taxonomy.php' ) ) {
	require_once 'related-posts-by-taxonomy.php';
}
if ( is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) ) {
	require_once 'yoast.php';
}
