<?php
/*
Plugin Name: Character CPT
Plugin URI:  http://lezwatchtv.com
Description: Custom Post Type for characters on LWTV
Version: 1.0
Author: Evan Herman, Mika Epstein
*/

/*
 * CPT Settings
 *
 */

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
		'supports'            => array( 'title', 'editor', 'thumbnail', 'genesis-cpt-archives-settings', 'genesis-seo', 'revisions' ),
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

/*
 * Custom Taxonomies
 *
 */
add_action( 'init', 'create_post_type_characters_taxonomies', 0 );
function create_post_type_characters_taxonomies() {

	// TROPES
	$names_tropes = array(
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
	$args_tropes = array(
		'hierarchical'          => false,
		'labels'                => $names_tropes,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'tropes' ),
	);
	register_taxonomy( 'lez_chartags', 'post_type_characters', $args_tropes );

	// GENDER IDENTITY
	$names_gender = array(
		'name'                       => _x( 'Gender', 'lezwatchtv' ),
		'singular_name'              => _x( 'Gender Identity', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Genders' ),
		'popular_items'              => __( 'Popular Genders' ),
		'all_items'                  => __( 'All Genders' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Gender' ),
		'update_item'                => __( 'Update Gender' ),
		'add_new_item'               => __( 'Add New Gender' ),
		'new_item_name'              => __( 'New Gender Name' ),
		'separate_items_with_commas' => __( 'Separate Genders with commas' ),
		'add_or_remove_items'        => __( 'Add or remove Genders' ),
		'choose_from_most_used'      => __( 'Choose from the most used Genders' ),
		'not_found'                  => __( 'No Genders found.' ),
		'menu_name'                  => __( 'Gender Identity' ),
	);
	$args_gender = array(
		'hierarchical'          => false,
		'labels'                => $names_gender,
        'public'                => true,
		'show_ui'               => true,
		'show_admin_column'     => true,
        'show_in_nav_menus'     => true,
        'show_tagcloud'         => false,
		'rewrite'               => array( 'slug' => 'gender' ),
	);
	register_taxonomy( 'lez_gender', 'post_type_characters', $args_gender );

	// SEXUALITY
	$names_sexuality = array(
		'name'                       => _x( 'Sexual Orientation', 'lezwatchtv' ),
		'singular_name'              => _x( 'Sexual Orientation', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Sexual Orientations' ),
		'popular_items'              => __( 'Popular Sexual Orientations' ),
		'all_items'                  => __( 'All Sexual Orientations' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Sexual Orientation' ),
		'update_item'                => __( 'Update Sexual Orientation' ),
		'add_new_item'               => __( 'Add New Sexual Orientation' ),
		'new_item_name'              => __( 'New Sexual Orientation Name' ),
		'separate_items_with_commas' => __( 'Separate Sexual Orientations with commas' ),
		'add_or_remove_items'        => __( 'Add or remove Sexual Orientations' ),
		'choose_from_most_used'      => __( 'Choose from the most used Sexual Orientations' ),
		'not_found'                  => __( 'No Sexual Orientations found.' ),
		'menu_name'                  => __( 'Sexual Orientation' ),
	);
	$args_sexuality = array(
		'hierarchical'          => false,
		'labels'                => $names_sexuality,
        'public'                => true,
		'show_ui'               => true,
		'show_admin_column'     => true,
        'show_in_nav_menus'     => true,
        'show_tagcloud'         => false,
		'rewrite'               => array( 'slug' => 'sexuality' ),
	);
	register_taxonomy( 'lez_sexuality', 'post_type_characters', $args_sexuality );

}

/*
 * Custom Meta Box section
 *
 * This relies fully on CMB2.
 *
 */

// This gets a list of all the shows.
function cmb2_get_post_type_shows_options() {
    return cmb2_get_post_options( array(
    		'post_type' => 'post_type_shows',
    		'numberposts' => -1,
    		'post_status' => array('publish', 'pending', 'draft', 'future'),
    	) );
}

add_filter( 'cmb2_admin_init', 'cmb_post_type_characters_metaboxes' );
function cmb_post_type_characters_metaboxes() {

	// prefix for all custom fields
	$prefix = 'lezchars_';

	// This is just an array of all years from 1970, don't look at me like that
	$year_first = 1970;
	$year_array = array();
	foreach (range(date('Y'), $year_first) as $x) {
		$year_array[$x] = $x;
	}

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
	// Field: Character Tropes
	$cmb_characters->add_field( array(
	    'name'     => 'Character Tropes',
	    'id'       => $prefix . 'tropes',
		'taxonomy' => 'lez_chartags', //Enter Taxonomy Slug
		'type'     => 'taxonomy_multicheck',
		'select_all_button' => false,
	) );
	// Field: Character Gender Idenity
	$cmb_characters->add_field( array(
		'name'       => 'Gender Identity',
		'desc'       => 'Gender with which the character identifies',
		'id'         => $prefix . 'gender',
		'taxonomy'   => 'lez_gender', //Enter Taxonomy Slug
		'type'       => 'taxonomy_radio_inline',
		'show_option_none' => false,
	) );
	// Field: Character Sexual Orientation
	$cmb_characters->add_field( array(
		'name'       => 'Sexuality',
		'desc'       => 'Character\'s sexual orientation',
		'id'         => $prefix . 'sexuality',
		'taxonomy'   => 'lez_sexuality', //Enter Taxonomy Slug
		'type'       => 'taxonomy_radio_inline',
		'show_option_none' => false,
	) );

	// Field: Show Name
	$cmb_characters->add_field( array(
		'name'             => 'Show',
		'desc'             => 'Select the show this character belongs to',
		'id'               => $prefix . 'show',
		'type'             => 'select',
		'repeatable' => 'true',
		'show_option_none' => true,
		'default'          => 'custom',
	    'options_cb'       => 'cmb2_get_post_type_shows_options',
	) );
	// Field: Character Type
	$cmb_characters->add_field( array(
	    'name'             => 'Character Type',
		'desc'             => 'Main characters are in credits. Guests show up once or twice. Recurring have their own plots.',
	    'id'               => $prefix .'type',
	    'type'             => 'select',
	    'show_option_none' => true,
	    'default'          => 'custom',
	    'options'          => array(
	        'regular'   => 'Regular/Main Character',
	        'recurring' => 'Recurring Character',
        	'guest'     => 'Guest Character',
	    ),
	) );
	$cmb_characters->add_field( array(
	    'name'             => 'Year of Death',
	    'desc'             => 'If the character is dead, select what year they died.',
	    'id'               => $prefix .'death_year',
	    'type'             => 'select',
	    'show_option_none' => true,
	    'default'          => 'custom',
	    'options'          => $year_array,
	) );
}

/*
 * Meta Box Adjustments
 *
 */

// Remove Metaboxes we use elsewhere
add_action( 'admin_menu', 'remove_meta_boxes_from_post_type_characters');
function remove_meta_boxes_from_post_type_characters() {
	remove_meta_box( 'tagsdiv-lez_gender', 'post_type_characters', 'side' );
	remove_meta_box( 'tagsdiv-lez_sexuality', 'post_type_characters', 'side' );
	remove_meta_box( 'tagsdiv-lez_chartags', 'post_type_characters', 'side' );
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

// Add shit to columns
add_filter( 'manage_post_type_characters_posts_columns', 'set_custom_edit_post_type_characters_columns' );
function set_custom_edit_post_type_characters_columns($columns) {

	$new_columns = array();
	foreach($columns as $key => $title) {
		if ($key=='date') {
			$new_columns['shows'] = 'TV Show';
			$new_columns['roletype'] = 'Role Type';
		}
		$new_columns[$key] = $title;
	}

	return $new_columns;
}

add_action( 'manage_post_type_characters_posts_custom_column' , 'custom_post_type_characters_column', 10, 2 );
add_action( 'manage_post_type_characters_posts_custom_column' , 'custom_post_type_characters_column', 10, 2 );
function custom_post_type_characters_column( $column, $post_id ) {

	// Since SOME characters have multiple shows, we force this to be an array
	if ( !is_array( get_post_meta( $post_id, 'lezchars_show', true ) ) ) {
		$character_show_IDs = array( get_post_meta( $post_id, 'lezchars_show', true ) );
	} else {
		$character_show_IDs = get_post_meta( $post_id, 'lezchars_show', true );
	}

	// Show Title is an array to handle fucking commas
	$show_title = array();

	foreach ( $character_show_IDs as $character_show_ID ) {
		array_push( $show_title, get_post( $character_show_ID )->post_title );
	}

	switch ( $column ) {
		case 'shows':
			echo implode(", ", $show_title );
			break;
		case 'roletype':
			echo ucfirst(get_post_meta( $post_id, 'lezchars_type', true ));
			break;
	}
}