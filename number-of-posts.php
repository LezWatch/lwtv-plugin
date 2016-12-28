<?php
/*
Plugin Name: Number of Posts
Description: Display Number of Posts via shortcodes
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

// [numtax term="term_slug" taxonomy="tax_slug"]
function lezwatch_numtax_shortcode( $atts ) {
	$attr = shortcode_atts( array(
		'term'     => '',
		'taxonomy' => '',
	), $atts );

	$the_term = sanitize_text_field( $attr['term'] );

	// Early Bailout
	if ( is_null($the_term) ) return "n/a";

	$all_taxonomies = ( empty( $attr['taxonomy'] ) )? get_taxonomies() : array( sanitize_text_field( $attr['taxonomy'] ) );

	//$all_taxonomies = get_taxonomies();
	foreach ( $all_taxonomies as $taxonomy ) {
	    $does_term_exist = term_exists( $the_term, $taxonomy );
	    if ( $does_term_exist !== 0 && $does_term_exist !== null ) {
		    $the_taxonomy = $taxonomy;
		    break;
	    } else {
		    $the_taxonomy = false;
	    }
	}

	// If no taxonomy, bail
	if ( $the_taxonomy == false ) return "n/a";

	$to_count = get_term_by( 'slug', $the_term, $the_taxonomy );
	return $to_count->count;

}
add_shortcode( 'numtax', 'lezwatch_numtax_shortcode' );