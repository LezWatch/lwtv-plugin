<?php

// Register Custom Post Type
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
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail' ),
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

// Hook into the 'init' action
add_action( 'init', 'lez_shows_post_type', 0 );

/** END Function to create and register custom post type **/


/** BEGIN Function to create and register custom post type Taxonomies **/

// hook into the init action and call create_post_type_shows_taxonomies when it fires
add_action( 'init', 'create_post_type_shows_taxonomies', 0 );

// create taxonomies
function create_post_type_shows_taxonomies() {
	// Add new taxonomy, NOT hierarchical (like tags)
	//Labels for the new taxonomy
	$names_gentags = array(
		'name'                       => _x( 'Station and Location', 'lezwatchtv' ),
		'singular_name'              => _x( 'Tag', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Station Tags' ),
		'popular_items'              => __( 'Popular Station Tags' ),
		'all_items'                  => __( 'All Station Tags' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Tag' ),
		'update_item'                => __( 'Update Tag' ),
		'add_new_item'               => __( 'Add New Tag' ),
		'new_item_name'              => __( 'New Tag Name' ),
		'separate_items_with_commas' => __( 'Separate Station Tags with commas' ),
		'add_or_remove_items'        => __( 'Add or remove Station Tags' ),
		'choose_from_most_used'      => __( 'Choose from the most used Station Tags' ),
		'not_found'                  => __( 'No Station Tags found.' ),
		'menu_name'                  => __( 'Station Tags' ),
	);
	//paramters for the new taxonomy
	$args_gentags = array(
		'hierarchical'          => false,
		'labels'                => $names_gentags,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'show-tags' ),
	);

	register_taxonomy( 'lez_tags', 'post_type_shows', $args_gentags );
}

/** END Function to create and register custom post type Taxonomies **/



/** BEGIN Function to create and register custom fields for custom post type **/
add_filter( 'cmb_meta_boxes', 'cmb_post_type_shows_metaboxes' );

function cmb_post_type_shows_metaboxes( array $meta_boxes ) {

	// prefix for all custom fields
	$prefix = 'lezshows_';
	
	$meta_boxes[] = array(
		'id'         => 'shows_metabox',
		'title'      => 'Show Details',
		'pages'      => array( 'post_type_shows', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			// use WP default wysiwyg editor
			array(
				'name'    => 'Queer Plotline Timeline',
				'desc'    => 'Which seasons/episodes have the gay in it',
				'id'      => $prefix . 'plots',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 10, ),
			),
			// use WP default wysiwyg editor
			array(
				'name'    => 'Notable Lez-Centric Episodes',
				'id'      => $prefix . 'episodes',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 10, ),
			),

			//checkboxes for custom taxonomy
			array(
				'name'		=> 'Cliché Plotlines',
				'id'		=> $prefix . 'cliches',
				'type'		=> 'taxonomy_multicheck',
				'taxonomy'	=> 'lez_cliches', // Taxonomy Name
			),
			// use WP default wysiwyg editor
			array(
				'name'    => 'Where to watch it',
				'id'      => $prefix . 'watch',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 10, ),
			),
			// simple text field for form
			array(
				'name' => 'Hashtags',
				'id'   => $prefix . 'hashtags',
				'type' => 'text',
			),
			// simple text field for form
			array(
				'name' => 'Fansite 1 Title',
				'id'   => $prefix . 'fansite1title',
				'type' => 'text',
			),
			array(
				'name' => 'Fansite 1 URL',
				'id'   => $prefix . 'fansite1url',
				'type' => 'text',
			),
			// simple text field for form
			array(
				'name' => 'Fansite 2 Title',
				'id'   => $prefix . 'fansite2title',
				'type' => 'text',
			),
			array(
				'name' => 'Fansite 2 URL',
				'id'   => $prefix . 'fansite2url',
				'type' => 'text',
			),
			// simple text field for form
			array(
				'name' => 'Fansite 3 Title',
				'id'   => $prefix . 'fansite3title',
				'type' => 'text',
			),
			array(
				'name' => 'Fansite 3 URL',
				'id'   => $prefix . 'fansite3url',
				'type' => 'text',
			),
			//multiple checkboxes
			array(
				'name'    => 'Special Stars',
				'id'      => $prefix . 'stars',
				'type'    => 'multicheck',
				'options' => array(
					'check1' => 'Gold Star',
					'check2' => 'Silver Star',
				),
			),
		),
	);

	// A second metabox
	$meta_boxes[] = array(
		'id'         => 'ratings_metabox',
		'title'      => 'Ratings',
		'pages'      => array( 'post_type_shows', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			//radio buttons displayed inline
			array(
				'name'    => 'Realness Rating',
				'id'      => $prefix . 'realness_rating',
				'type'    => 'radio_inline',
				'options' => array(
					array( 'name' => '1', 'value' => '1', ),
					array( 'name' => '2', 'value' => '2', ),
					array( 'name' => '3', 'value' => '3', ),
					array( 'name' => '4', 'value' => '4', ),
					array( 'name' => '5', 'value' => '5', ),
					array( 'name' => '6', 'value' => '6', ),
				),
			),
			// use WP default wysiwyg editor
			array(
				'name'    => 'Realness details',
				'id'      => $prefix . 'realness_details',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 5, ),
			),
			//radio buttons displayed inline
			array(
				'name'    => 'Show Quality Rating',
				'id'      => $prefix . 'quality_rating',
				'type'    => 'radio_inline',
				'options' => array(
					array( 'name' => '1', 'value' => '1', ),
					array( 'name' => '2', 'value' => '2', ),
					array( 'name' => '3', 'value' => '3', ),
					array( 'name' => '4', 'value' => '4', ),
					array( 'name' => '5', 'value' => '5', ),
					array( 'name' => '6', 'value' => '6', ),
				),
			),
			array(
				'name'    => 'Show Quality Details',
				'id'      => $prefix . 'quality_details',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 5, ),
			),
			//radio buttons displayed inline
			array(
				'name'    => 'Screen Time Rating',
				'id'      => $prefix . 'screentime_rating',
				'type'    => 'radio_inline',
				'options' => array(
					array( 'name' => '1', 'value' => '1', ),
					array( 'name' => '2', 'value' => '2', ),
					array( 'name' => '3', 'value' => '3', ),
					array( 'name' => '4', 'value' => '4', ),
					array( 'name' => '5', 'value' => '5', ),
					array( 'name' => '6', 'value' => '6', ),
				),
			),
			array(
				'name'    => 'Screen Time Details',
				'id'      => $prefix . 'screentime_details',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 5, ),
			),
			//radio buttons displayed inline
			array(
				'name'    => 'Worth It?',
				'id'      => $prefix . 'worthit_rating',
				'type'    => 'radio_inline',
				'options' => array(
					array( 'name' => 'Yes', 'value' => 'Yes', ),
					array( 'name' => 'Meh', 'value' => 'Meh', ),
					array( 'name' => 'No', 'value' => 'No', ),
				),
			),
			array(
				'name'    => 'Worth It Details',
				'id'      => $prefix . 'worthit_details',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 5, ),
			),
		)
	);

	return $meta_boxes;
}

/** END Function to create and register custom fields for custom post type **/


/** BEGIN Function hide custom taxonomies metabox **/
	//function to initiate metaboxes to remove
	function remove_meta_boxes_from_post_type_shows() {
		//function to pick metaboxes to remove
		function the_meta_boxes_to_remove() {
			//Remove From providers
			remove_meta_box( 'tagsdiv-lez_cliches', 'post_type_shows', 'side' ); // for tag type custom taxonomies
		}
		add_action( 'admin_menu' , 'the_meta_boxes_to_remove' );
	}

add_action( 'init', 'remove_meta_boxes_from_post_type_shows');

/** END Function hide custom taxonomies metabox **/


/** BEGIN Filter to change the default "Enter title here" Text **/

add_filter( 'enter_title_here', 'enter_title_here_post_type_shows' );
function enter_title_here_post_type_shows( $message ){
  global $post;
  if( 'post_type_shows' == $post->post_type ):
    $message = 'Enter Show Title';
  endif;

  return $message;
}

/** END Filter to change the default "Enter title here" Text **/


/** BEGIN Function to change the default "Featured Image" metabox Title **/

add_action('do_meta_boxes', 'featured_image_title_post_type_shows');
function featured_image_title_post_type_shows()
{
    remove_meta_box( 'postimagediv', 'post_type_shows', 'side' );
    add_meta_box('postimagediv', __('Show Image'), 'post_thumbnail_meta_box', 'post_type_shows', 'side');
}

/** END Function to change the default "Featured Image" metabox Title **/


/** BEGIN Filter to change the default "Set Featured Image" Text **/

function set_featured_image_text_post_type_shows( $content ) {
    global $current_screen;
 
    if( 'post_type_shows' == $current_screen->post_type )
        return $content = str_replace( __( 'Set featured image' ), __( 'Upload Show Image' ), $content);
    else
        return $content;
}
add_filter( 'admin_post_thumbnail_html', 'set_featured_image_text_post_type_shows' );

/** END Filter to change the default "Set Featured Image" Text **/

