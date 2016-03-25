<?php
/*
Plugin Name: CMB2 Bootstrap
Plugin URI:  https://github.com/WebDevStudios/CMB2
Description: Boostrap file to load CMB2 and everything it needs to be running. Since we're using this as an MU plugin, it's required.
Version: 1.0
Author: Mika Epstein
*/

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

// Extra Get post options.

function cmb2_get_post_options( $query_args ) {

    $args = wp_parse_args( $query_args, array(
        'post_type'   => 'post',
        'numberposts' => -1,
    ) );

    $posts = get_posts( $args );

    $post_options = array();
    if ( $posts ) {
        foreach ( $posts as $post ) {
          $post_options[ $post->ID ] = $post->post_title;
        }
    }
    
    asort($post_options);

    return $post_options;
}

// Handle the CSS for this
function cmb2_lez_scripts( $hook ) {
	if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {
		wp_register_style( 'cmb-styles', plugins_url('/cmb2.css', __FILE__ ) );
		wp_enqueue_style( 'cmb-styles' );
	}
}
add_action( 'admin_enqueue_scripts', 'cmb2_lez_scripts', 10 );