<?php

/*
Plugin Name: Base Functions
Plugin URI:  http://lezwatchtv.com
Description: This file calls all the children files, depending on what other
Version: 1.0
Author: Mika Epstein
*/

// Functions to run if certain plugins are active
function lez_check_admin_plugins() {
	// If WP Help is active, call customizations
	if ( is_plugin_active( 'wp-help/wp-help.php' ) ) {
		require_once( 'plugins/wp-help.php' );
	}

	// If Yoast SEO is active, call customizations
	if (is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
		require_once( 'plugins/yoast-seo.php' );
	}
}
add_action( 'admin_init', 'lez_check_admin_plugins' );

// Call CMB2 - it doesn't error if it's not there
require_once( 'plugins/cmb2.php' );

// Include CPTs
include( 'cpts/characters.php' );
include( 'cpts/shows.php' );

// Customize Featured images for CPTs
add_action( 'admin_init', 'lez_featured_images' );
function lez_featured_images() {
	$post_type_args = array(
	   'public'   => true,
	   '_builtin' => false
	);
	$post_types = get_post_types( $post_type_args, 'objects' );
	foreach ( $post_types as $post_type ) {

		$type = $post_type->name;
		$name = $post_type->labels->singular_name;

		// change the default "Featured Image" metabox title
		add_action('do_meta_boxes', function() use ( $type, $name ) {
			remove_meta_box( 'postimagediv', $type, 'side' );
			add_meta_box('postimagediv', $name.' Image', 'post_thumbnail_meta_box', $type, 'side');
		});

		// change the default "Set Featured Image" text
		add_filter( 'admin_post_thumbnail_html', function( $content ) use ( $type, $name ) {
			global $current_screen;
			if( !is_null($current_screen) && $type == $current_screen->post_type ) {
			    // Get featured image size
			    global $_wp_additional_image_sizes;
			    $genesis_image_size = rtrim( str_replace( 'post_type_', '', $type ), 's' ).'-img';
			    if ( isset( $_wp_additional_image_sizes[ $genesis_image_size ] ) ) {
			        $content = '<p>Image Size: ' . $_wp_additional_image_sizes[$genesis_image_size]['width'] . 'x' . $_wp_additional_image_sizes[$genesis_image_size]['height'] . 'px</p>' . $content;
			    }
				$content = str_replace( __( 'featured' ), strtolower( $name ) , $content);
			}
			return $content;
		});
	}
}