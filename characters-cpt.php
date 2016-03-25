<?php
/*
Plugin Name: Character CPT
Plugin URI:  http://lezwatchtv.com
Description: Custom Post Type for characters on LWTV
Version: 1.0
Author: Evan Herman, Mika Epstein
*/

// Register Custom Post Type
add_action( 'init', 'lez_characters_post_type', 0 );
function lez_characters_post_type() {
	$labels = array(
		'name'                => _x( 'Characters', 'Post Type General Name', 'lezwatchtv' ),
		'singular_name'       => _x( 'Character', 'Post Type Singular Name', 'lezwatchtv' ),
		'menu_name'           => __( 'Characters', 'lezwatchtv' ),
		'parent_item_colon'   => __( 'Parent Character:', 'lezwatchtv' ),
		'all_items'           => __( 'All Characters', 'lezwatchtv' ),
		'view_item'           => __( 'View Character', 'lezwatchtv' ),
		'add_new_item'        => __( 'Add New Character', 'lezwatchtv' ),
		'add_new'             => __( 'Add New', 'lezwatchtv' ),
		'edit_item'           => __( 'Edit Character', 'lezwatchtv' ),
		'update_item'         => __( 'Update Character', 'lezwatchtv' ),
		'search_items'        => __( 'Search Characters', 'lezwatchtv' ),
		'not_found'           => __( 'No characters found', 'lezwatchtv' ),
		'not_found_in_trash'  => __( 'No characters in the Trash', 'lezwatchtv' ),
	);
	$args = array(
		'label'               => __( 'post_type_characters', 'lezwatchtv' ),
		'description'         => __( 'Characters', 'lezwatchtv' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'genesis-cpt-archives-settings', 'genesis-seo' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
	 	'rewrite' 			  => array( 'slug' => 'characters' ),
		'menu_icon'           => 'dashicons-nametag',
		'menu_position'       => 7,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'post_type_characters', $args );
}

// hook into the init action and call create_post_type_characters_taxonomies when it fires
add_action( 'init', 'create_post_type_characters_taxonomies', 0 );
function create_post_type_characters_taxonomies() {
	// Add new taxonomy, NOT hierarchical (like tags)
	//Labels for the new taxonomy
	$names_chartags = array(
		'name'                       => _x( 'Character Tropes', 'lezwatchtv' ),
		'singular_name'              => _x( 'Trope', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Tropes' ),
		'popular_items'              => __( 'Popular Tropes' ),
		'all_items'                  => __( 'All Tropes' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Trope' ),
		'update_item'                => __( 'Update Trope' ),
		'add_new_item'               => __( 'Add New Trope' ),
		'new_item_name'              => __( 'New Trope Name' ),
		'separate_items_with_commas' => __( 'Separate Tropes with commas' ),
		'add_or_remove_items'        => __( 'Add or remove Tropes' ),
		'choose_from_most_used'      => __( 'Choose from the most used Tropes' ),
		'not_found'                  => __( 'No Tropes found.' ),
		'menu_name'                  => __( 'Tropes' ),
	);
	//paramters for the new taxonomy
	$args_chartags = array(
		'hierarchical'          => false,
		'labels'                => $names_chartags,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'tropes' ),
	);
	register_taxonomy( 'lez_chartags', 'post_type_characters', $args_chartags );
}

/*
 * Custom Meta Box section
 *
 * This relies fully on CMB2.
 *
 */

// This gets a list of all the shows.
function cmb2_get_post_type_shows_options() {
    return cmb2_get_post_options( array( 'post_type' => 'post_type_shows', 'numberposts' => -1 ) );
}

add_filter( 'cmb2_admin_init', 'cmb_post_type_characters_metaboxes' );
function cmb_post_type_characters_metaboxes() {

	// prefix for all custom fields
	$prefix = 'lezchars_';

	// MetaBox Group: Character Details
	$cmb_characters = new_cmb2_box( array(
		'id'            => 'chars_metabox',
		'title'         => 'Character Details',
		'object_types'  => array( 'post_type_characters', ), // Post type
		'context'       => 'normal',
		'priority'      => 'high',
		'show_names   ' => true, // Show field names on the left
	) );

	// Field: Actor Name
	$cmb_characters->add_field( array(
		'name'       => 'Actor Name',
		'desc'       => 'Include years (in parens) for multiple actors',
		'id'         => $prefix . 'actor',
		'type'       => 'text',
		'repeatable' => 'true',
	) );
	// Field: Show Name
	$cmb_characters->add_field( array(
		'name'             => 'Show',
		'desc'             => 'Select the show this character belongs to',
		'id'               => $prefix . 'show',
		'type'             => 'select',
		'show_option_none' => true,
		'default'          => 'custom',
	    'options_cb'       => 'cmb2_get_post_type_shows_options',
	) );

	$cmb_characters->add_field( array(
	    'name'             => 'Character Type',
	    'id'               => $prefix .'type',
	    'type'             => 'select',
	    'show_option_none' => false,
	    'default'          => 'custom',
	    'options'          => array(
	        'regular'   => 'Regular/Main Character',
	        'guest'     => 'Guest Character',
	        'recurring' => 'Recurring Character',
	    ),
	) );
	
	// Field: IMDB URL
	$cmb_characters->add_field( array(
		'name'      => 'IMDB URL',
		'id'        => $prefix . 'url',
		'type'      => 'text_url',
		'protocols' => array('http', 'https'), // Array of allowed protocols
	) );
}

// change the default "Featured Image" metabox title
add_action('do_meta_boxes', 'featured_image_title_post_type_characters');
function featured_image_title_post_type_characters() {
    remove_meta_box( 'postimagediv', 'post_type_characters', 'side' );
    add_meta_box('postimagediv', __('Character Photo'), 'post_thumbnail_meta_box', 'post_type_characters', 'side');
}

// change the default "Set Featured Image" text
add_filter( 'admin_post_thumbnail_html', 'set_featured_image_text_post_type_characters' );
function set_featured_image_text_post_type_characters( $content ) {
    global $current_screen;

    if( 'post_type_characters' == $current_screen->post_type ) {
        return $content = str_replace( __( 'Set featured image' ), __( 'Upload Character Photo' ), $content);
    } else {
        return $content;
    }
}