<?php
/*
Plugin Name: Number of Posts
Description: Display Number of Posts
Version: 1.0
Author: Mika Epstein
*/

// [numposts type="posts"]
function lezwatch_numposts_shortcode( $atts ) {
	$attr = shortcode_atts( array(
		'type' => 'post',
	), $atts );

	$posttype = sanitize_text_field( $attr['type'] );
	if ( post_type_exists( $posttype ) !== true ) $posttype = 'post';

	$to_count = wp_count_posts( $posttype );

	return $to_count->publish;

}
add_shortcode( 'numposts', 'lezwatch_numposts_shortcode' );