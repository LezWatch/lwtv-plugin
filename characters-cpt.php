<?php

// Register Custom Post Type
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
		'supports'            => array( 'title', 'thumbnail', 'genesis-cpt-archives-settings', 'genesis-seo' ),
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

// Hook into the 'init' action
add_action( 'init', 'lez_characters_post_type', 0 );

/** END Function to create and register custom post type **/


/** BEGIN Function to create and register custom post type Taxonomies **/

// hook into the init action and call create_post_type_characters_taxonomies when it fires
add_action( 'init', 'create_post_type_characters_taxonomies', 0 );

// create taxonomies
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

/** END Function to create and register custom post type Taxonomies **/



/** BEGIN Function to create and register custom fields for custom post type **/
add_filter( 'cmb_meta_boxes', 'cmb_post_type_characters_metaboxes' );

function cmb_post_type_characters_metaboxes( array $meta_boxes ) {

	// prefix for all custom fields
	$prefix = 'lezchars_';

	$meta_boxes[] = array(
		'id'         => 'chars_metabox',
		'title'      => 'Character Details',
		'pages'      => array( 'post_type_characters', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Character field names on the left
		'fields'     => array(
			// use WP default wysiwyg editor
			array(
				'name'    => 'Character description',
				'id'      => $prefix . 'description',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 10, ),
			),
			// simple text field for form
			array(
				'name' => 'Actor Name',
				'id'   => $prefix . 'actor',
				'type' => 'text',
			),
			//select (dropdown menu) to link to other custom post types
			array(
				'name' => 'Show',
				'desc' => 'Select the show this character belongs to',
				'id'   => $prefix . 'show',
				'type' => 'select_post_type',
				'post-type' => 'post_type_shows', // CPT
			),
			// simple text field for form
			array(
				'name' => 'External Site Title',
				'desc' => 'Wikipedia, IMdB, etc.',
				'id'   => $prefix . 'sitetitle',
				'type' => 'text',
			),
			array(
				'name' => 'External Site URL',
				'desc' => 'Include http://',
				'id'   => $prefix . 'url',
				'type' => 'text',
			),
		),
	);

	return $meta_boxes;
}

/** END Function to create and register custom fields for custom post type **/

/** BEGIN Function to change the default "Featured Image" metabox Title **/

add_action('do_meta_boxes', 'featured_image_title_post_type_characters');
function featured_image_title_post_type_characters()
{
    remove_meta_box( 'postimagediv', 'post_type_characters', 'side' );
    add_meta_box('postimagediv', __('Character Photo'), 'post_thumbnail_meta_box', 'post_type_characters', 'side');
}

/** END Function to change the default "Featured Image" metabox Title **/


/** BEGIN Filter to change the default "Set Featured Image" Text **/

function set_featured_image_text_post_type_characters( $content ) {
    global $current_screen;

    if( 'post_type_characters' == $current_screen->post_type )
        return $content = str_replace( __( 'Set featured image' ), __( 'Upload Character Photo' ), $content);
    else
        return $content;
}
add_filter( 'admin_post_thumbnail_html', 'set_featured_image_text_post_type_characters' );

/** END Filter to change the default "Set Featured Image" Text **/

