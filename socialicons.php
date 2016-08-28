<?php
/*
Plugin Name: Social Media Icons
Plugin URI: http://lezwatchtv.com/
Description: Embed social media icons (which are SVG) as shortcodes
Version: 1.0
Author: Mika Epstein
Author URI: http://ipstenu.org/
Author Email: ipstenu@halfelf.org

Copyright (C) 2016 Mika Epstein.
*/

function generate_social_shortcode($atts) {
	$iconsfolder = plugin_dir_path( __FILE__ ) . '/socialicons/';
    $svg = shortcode_atts( array(
    'file'	=> '',
    'title'	=> '',
    'url'	=> '',
    ), $atts );

    if ( !file_exists( $iconsfolder.$svg['file'].'.svg' ) ) {
	    $svg['file'] = 'square';
    }

	$iconpath = '<span role="img" aria-label="'. sanitize_text_field($svg['title']).'" title="'.sanitize_text_field($svg['title']).'" class="svg-shortcode '.sanitize_text_field($svg['title']).'">';
	if ( !empty($svg['url']) ) {
		$iconpath .= '<a href='.esc_url( $svg['url'] ).'>'.file_get_contents( $iconsfolder.$svg['file'].'.svg' ).'</a>';
	} else {
		$iconpath .= file_get_contents( $iconsfolder.$svg['file'].'.svg' );
	}
	$iconpath .= '</span>';

	return $iconpath;
}

add_shortcode('svgicon', 'generate_social_shortcode');
add_shortcode('socialicon', 'generate_social_shortcode');
add_filter('widget_text', 'do_shortcode', 7);