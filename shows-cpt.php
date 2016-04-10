<?php
/*
Plugin Name: Shows CPT
Plugin URI:  http://lezwatchtv.com
Description: Custom Post Type for shows on LWTV
Version: 1.0
Author: Evan Herman, Mika Epstein
*/

// Register Custom Post Type
add_action( 'init', 'lez_shows_post_type', 0 );
function lez_shows_post_type() {

	$labels = array(
		'name'                => _x( 'TV Shows', 'Post Type General Name', 'lezwatchtv' ),
		'singular_name'       => _x( 'TV Show', 'Post Type Singular Name', 'lezwatchtv' ),
		'menu_name'           => __( 'TV Shows', 'lezwatchtv' ),
		'parent_item_colon'   => __( 'Parent Show:', 'lezwatchtv' ),
		'all_items'           => __( 'All Shows', 'lezwatchtv' ),
		'view_item'           => __( 'View Show', 'lezwatchtv' ),
		'add_new_item'        => __( 'Add New Show', 'lezwatchtv' ),
		'add_new'             => __( 'Add New', 'lezwatchtv' ),
		'edit_item'           => __( 'Edit Show', 'lezwatchtv' ),
		'update_item'         => __( 'Update Show', 'lezwatchtv' ),
		'search_items'        => __( 'Search Shows', 'lezwatchtv' ),
		'not_found'           => __( 'Not found', 'lezwatchtv' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'lezwatchtv' ),
	);
	$args = array(
		'label'               => __( 'post_type_shows', 'lezwatchtv' ),
		'description'         => __( 'TV Shows', 'lezwatchtv' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'genesis-cpt-archives-settings', 'genesis-seo', 'revisions' ),
		'taxonomies'          => array( 'lez_cliches' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
	 	'rewrite' 			  => array( 'slug' => 'shows' ),
		'menu_icon'           => 'dashicons-video-alt',
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'post_type_shows', $args );

}

/*
 * TAXONOMIES
 *
 */

add_action( 'init', 'create_post_type_shows_taxonomies', 0 );
function create_post_type_shows_taxonomies() {

	// TV STATIONS
	$names_tvstations = array(
		'name'                       => _x( 'TV Station', 'lezwatchtv' ),
		'singular_name'              => _x( 'Station', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Stations' ),
		'popular_items'              => __( 'Popular Stations' ),
		'all_items'                  => __( 'All Stations' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Station' ),
		'update_item'                => __( 'Update Station' ),
		'add_new_item'               => __( 'Add New Station' ),
		'new_item_name'              => __( 'New Station Name' ),
		'separate_items_with_commas' => __( 'Separate Stations with commas' ),
		'add_or_remove_items'        => __( 'Add or remove Stations' ),
		'choose_from_most_used'      => __( 'Choose from the most used Stations' ),
		'not_found'                  => __( 'No Stations found.' ),
		'menu_name'                  => __( 'Stations' ),
	);
	//paramters for the new taxonomy
	$args_tvstations = array(
		'hierarchical'          => false,
		'labels'                => $names_tvstations,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'stations' ),
	);

	register_taxonomy( 'lez_tags', 'post_type_shows', $args_tvstations );

	// SHOW CLICHES
    $names_cliches = array(
        'name'                       => _x( 'Show Clichés', 'Taxonomy General Name', 'lezwatchtv' ),
        'singular_name'              => _x( 'Cliché', 'Taxonomy Singular Name', 'lezwatchtv' ),
        'menu_name'                  => __( 'Clichés', 'lezwatchtv' ),
        'all_items'                  => __( 'All Clichés', 'lezwatchtv' ),
        'parent_item'                => __( 'Parent Cliché', 'lezwatchtv' ),
        'parent_item_colon'          => __( 'Parent Cliché:', 'lezwatchtv' ),
        'new_item_name'              => __( 'New Cliché', 'lezwatchtv' ),
        'add_new_item'               => __( 'Add New Cliché', 'lezwatchtv' ),
        'edit_item'                  => __( 'Edit Cliché', 'lezwatchtv' ),
        'update_item'                => __( 'Update Cliché', 'lezwatchtv' ),
        'separate_items_with_commas' => __( 'Separate cliché names with commas', 'lezwatchtv' ),
        'search_items'               => __( 'Search Clichés', 'lezwatchtv' ),
        'add_or_remove_items'        => __( 'Add or remove clichés', 'lezwatchtv' ),
        'choose_from_most_used'      => __( 'Choose from the most used cliché name', 'lezwatchtv' ),
        'not_found'                  => __( 'Not Found', 'lezwatchtv' ),
    );
    $args_cliches = array(
        'hierarchical'               => false,
        'labels'                     => $names_cliches,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'rewrite' 				  	 => array( 'slug' => 'cliches' ),
    );
    register_taxonomy( 'lez_cliches', array( 'post_type_shows' ), $args_cliches );
}

add_filter( 'cmb2_admin_init', 'cmb_post_type_shows_metaboxes' );
function cmb_post_type_shows_metaboxes() {

	// prefix for all custom fields
	$prefix = 'lezshows_';

	// Basic Show Details

	$cmb_showdetails = new_cmb2_box( array(
		'id'            => 'shows_metabox',
		'title'         => 'Shows Details',
		'object_types'  => array( 'post_type_shows', ), // Post type
		'context'       => 'normal',
		'priority'      => 'high',
		'show_names   ' => true, // Show field names on the left
	) );

	$cmb_showdetails->add_field( array(
	    'name'     => 'Cliché Plotlines',
	    'id'       => $prefix . 'cliches',
		'taxonomy' => 'lez_cliches', //Enter Taxonomy Slug
		'type'     => 'taxonomy_multicheck',
		'select_all_button' => false,
	) );

	$cmb_showdetails->add_field( array(
		'name'    => 'Queer Plotline Timeline',
		'desc'    => 'Which seasons/episodes have the gay in it',
		'id'      => $prefix . 'plots',
		'type'    => 'wysiwyg',
		'options' => array( 'textarea_rows' => 10, ),
	) );

	$cmb_showdetails->add_field( array(
		'name'    => 'Notable Lez-Centric Episodes',
		'id'      => $prefix . 'episodes',
		'type'    => 'wysiwyg',
		'options' => array(	'textarea_rows' => 10, ),
	) );

	// Box for Ratings
	$cmb_ratings = new_cmb2_box( array(
		'id'            => 'ratings_metabox',
		'title'         => 'Show Ratings',
		'desc'          => 'Ratings are subjective 1 to 5, with 1 being low and 5 being the L Word.',
		'object_types'  => array( 'post_type_shows', ), // Post type
		'context'       => 'normal',
		'priority'      => 'high',
		'show_names   ' => true, // Show field names on the left
	) );

	$cmb_ratings->add_field( array(
	    'name'    => 'Realness Rating',
	    'id'      => $prefix . 'realness_rating',
	    'desc'    => 'How realistic are the lesbians?',
	    'type'    => 'radio_inline',
	    'options' => array(
	        '1' => '1',
	        '2' => '2',
	        '3' => '3',
	        '4' => '4',
	        '5' => '5',
	    ),
	) );

	$cmb_ratings->add_field( array(
		'name'    => 'Realness details',
		'id'      => $prefix . 'realness_details',
		'type'    => 'wysiwyg',
		'options' => array(	'textarea_rows' => 5, ),
	) );

	$cmb_ratings->add_field( array(
	    'name'    => 'Show Quality Rating',
	    'id'      => $prefix . 'quality_rating',
	    'desc'    => 'How good is the show for lesbians?',
	    'type'    => 'radio_inline',
	    'options' => array(
	        '1' => '1',
	        '2' => '2',
	        '3' => '3',
	        '4' => '4',
	        '5' => '5',
	    ),
	) );

	$cmb_ratings->add_field( array(
		'name'    => 'Show Quality details',
		'id'      => $prefix . 'quality_details',
		'type'    => 'wysiwyg',
		'options' => array(	'textarea_rows' => 5, ),
	) );

	$cmb_ratings->add_field( array(
	    'name'    => 'Screentime Rating',
	    'id'      => $prefix . 'screentime_rating',
	    'desc'    => 'How much air-time do the lesbians get?',
	    'type'    => 'radio_inline',
	    'options' => array(
	        '1' => '1',
	        '2' => '2',
	        '3' => '3',
	        '4' => '4',
	        '5' => '5',
	    ),
	) );

	$cmb_ratings->add_field( array(
		'name'    => 'Screntime details',
		'id'      => $prefix . 'screentime_details',
		'type'    => 'wysiwyg',
		'options' => array(	'textarea_rows' => 5, ),
	) );

	$cmb_ratings->add_field( array(
	    'name'    => 'Worth It?',
	    'id'      => $prefix . 'worthit_rating',
	    'desc'    => 'Is the show worth watching?',
	    'type'    => 'radio_inline',
	    'options' => array(
	        'Yes' => 'Yes',
	        'Meh' => 'Meh',
	        'No'  => 'No',
	    ),
	) );

	$cmb_ratings->add_field( array(
		'name'    => 'Worth It details',
		'id'      => $prefix . 'worthit_details',
		'type'    => 'wysiwyg',
		'options' => array(	'textarea_rows' => 5, ),
	) );

	// Box for simple options
	$cmb_notes = new_cmb2_box( array(
		'id'            => 'notes_metabox',
		'title'         => 'Special Notes',
		'object_types'  => array( 'post_type_shows', ), // Post type
		'context'       => 'side',
		'priority'      => 'high',
		'show_names'	=> true, // Show field names on the left
		'cmb_styles'	=> false,
	) );

	$cmb_notes->add_field( array(
	    'name'				=> 'Awesome Stars',
	    'id'    			=> $prefix . 'stars',
	    'type'				=> 'select',
	    'show_option_none'	=> 'No Stars',
	    'options'	 => array(
			'gold'   => 'Gold Star',
			'silver' => 'Silver Star',
	    )
	) );

	$cmb_notes->add_field( array(
	    'name' => 'Trigger Warnings?',
	    'id'   => $prefix . 'triggerwarning',
	    'type' => 'checkbox'
	) );

}

//add_meta_box('postimagediv', __('Show Image'), 'post_thumbnail_meta_box', 'post_type_shows', 'side');

// function to initiate metaboxes to remove
add_action( 'init', 'remove_meta_boxes_from_post_type_shows');
function remove_meta_boxes_from_post_type_shows() {
	//function to pick metaboxes to remove
	function the_meta_boxes_to_remove() {
		// Remove the traditional cliche box since we're using it elsewhere.
		remove_meta_box( 'tagsdiv-lez_cliches', 'post_type_shows', 'side' ); // for tag type custom taxonomies
	}
	add_action( 'admin_menu' , 'the_meta_boxes_to_remove' );
}

// change the default "Featured Image" metabox Title
add_action('do_meta_boxes', 'featured_image_title_post_type_shows');
function featured_image_title_post_type_shows() {
    remove_meta_box( 'postimagediv', 'post_type_shows', 'side' );
    add_meta_box('postimagediv', __('Show Image'), 'post_thumbnail_meta_box', 'post_type_shows', 'side');
}

// change the default "Set Featured Image" text
add_filter( 'admin_post_thumbnail_html', 'set_featured_image_text_post_type_shows' );
function set_featured_image_text_post_type_shows( $content ) {
    global $current_screen;

    if( 'post_type_shows' == $current_screen->post_type )
        return $content = str_replace( __( 'Set featured image' ), __( 'Upload Show Image' ), $content);
    else
        return $content;
}
