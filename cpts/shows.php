<?php
/*
 * Custom Post Type for Shows on LWTV
 *
 * @since 1.0
 * Author: Evan Herman, Tracy Levesque, Mika Epstein
 */


/**
 * CSS tweaks
 */
add_action( 'admin_enqueue_scripts', 'shows_lwtv_scripts', 10 );
function shows_lwtv_scripts( $hook ) {
	global $current_screen;
	wp_register_style( 'shows-styles', plugins_url('shows.css', __FILE__ ) );
	if( 'post_type_shows' == $current_screen->post_type || 'lez_stations' == $current_screen->taxonomy || 'lez_tropes' == $current_screen->taxonomy ) {
		wp_enqueue_style( 'shows-styles' );
	}
}

/**
 * Custom Post Type
 */
add_action( 'init', 'lwtv_shows_post_type', 0 );
function lwtv_shows_post_type() {

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
		'taxonomies'          => array( 'lez_tropes' ),
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
		'name'                       => _x( 'TV Station(s)', 'lezwatchtv' ),
		'singular_name'              => _x( 'TV Station', 'lezwatchtv' ),
		'search_items'               => __( 'Search Stations', 'lezwatchtv' ),
		'popular_items'              => __( 'Popular Stations', 'lezwatchtv' ),
		'all_items'                  => __( 'All Stations', 'lezwatchtv' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Station', 'lezwatchtv' ),
		'update_item'                => __( 'Update Station', 'lezwatchtv' ),
		'add_new_item'               => __( 'Add New Station', 'lezwatchtv' ),
		'new_item_name'              => __( 'New Station Name', 'lezwatchtv' ),
		'separate_items_with_commas' => __( 'Separate Stations with commas', 'lezwatchtv' ),
		'add_or_remove_items'        => __( 'Add or remove Stations', 'lezwatchtv' ),
		'choose_from_most_used'      => __( 'Choose from the most used Stations', 'lezwatchtv' ),
		'not_found'                  => __( 'No Stations found.', 'lezwatchtv' ),
		'menu_name'                  => __( 'TV Stations', 'lezwatchtv' ),
	);
	//parameters for the new taxonomy
	$args_tvstations = array(
		'hierarchical'          => false,
		'labels'                => $names_tvstations,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
        'show_in_nav_menus'		=> true,
		'rewrite'               => array( 'slug' => 'stations' ),
	);

	register_taxonomy( 'lez_stations', 'post_type_shows', $args_tvstations );

	// SHOW TROPES
    $names_tropes = array(
        'name'							=> _x( 'Show Tropes', 'Taxonomy General Name', 'lezwatchtv' ),
        'singular_name'					=> _x( 'Trope', 'Taxonomy Singular Name', 'lezwatchtv' ),
        'menu_name'						=> __( 'Tropes', 'lezwatchtv' ),
        'all_items'						=> __( 'All Tropes', 'lezwatchtv' ),
        'parent_item'					=> __( 'Parent Trope', 'lezwatchtv' ),
        'parent_item_colon'				=> __( 'Parent Trope:', 'lezwatchtv' ),
        'new_item_name'					=> __( 'New Trope', 'lezwatchtv' ),
        'add_new_item'					=> __( 'Add New Trope', 'lezwatchtv' ),
        'edit_item'						=> __( 'Edit Trope', 'lezwatchtv' ),
        'update_item'					=> __( 'Update Trope', 'lezwatchtv' ),
        'separate_items_with_commas'	=> __( 'Separate tropes with commas', 'lezwatchtv' ),
        'search_items'					=> __( 'Search Tropes', 'lezwatchtv' ),
        'add_or_remove_items'			=> __( 'Add or remove tropes', 'lezwatchtv' ),
        'choose_from_most_used'			=> __( 'Choose from the most used tropes', 'lezwatchtv' ),
        'not_found'						=> __( 'Not Found', 'lezwatchtv' ),
    );
    $args_tropes = array(
        'hierarchical'			=> true,
        'labels'				=> $names_tropes,
        'public'				=> true,
        'show_ui'				=> true,
        'show_admin_column'		=> true,
        'show_in_nav_menus'		=> true,
        'show_tagcloud'			=> false,
        'rewrite'				=> array( 'slug' => 'tropes' ),
    );
    register_taxonomy( 'lez_tropes', array( 'post_type_shows' ), $args_tropes );

	// SHOW Forat
	$names_showformat = array(
		'name'                       => _x( 'Show Format', 'lezwatchtv' ),
		'singular_name'              => _x( 'Show Format', 'lezwatchtv' ),
		'search_items'               => __( 'Search Formats', 'lezwatchtv' ),
		'popular_items'              => __( 'Popular Formats', 'lezwatchtv' ),
		'all_items'                  => __( 'All Formats', 'lezwatchtv' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Format', 'lezwatchtv' ),
		'update_item'                => __( 'Update Format', 'lezwatchtv' ),
		'add_new_item'               => __( 'Add New Format', 'lezwatchtv' ),
		'new_item_name'              => __( 'New Format Name', 'lezwatchtv' ),
		'separate_items_with_commas' => __( 'Separate Formats with commas', 'lezwatchtv' ),
		'add_or_remove_items'        => __( 'Add or remove Formats', 'lezwatchtv' ),
		'choose_from_most_used'      => __( 'Choose from the most used Formats', 'lezwatchtv' ),
		'not_found'                  => __( 'No Formats found.', 'lezwatchtv' ),
		'menu_name'                  => __( 'Show Formats', 'lezwatchtv' ),
	);
	//parameters for the new taxonomy
	$args_showformat = array(
		'hierarchical'          => false,
		'labels'                => $names_showformat,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'query_var'             => true,
        'show_in_nav_menus'		=> true,
		'rewrite'               => array( 'slug' => 'format' ),
	);

	register_taxonomy( 'lez_formats', 'post_type_shows', $args_showformat );
}

add_filter( 'cmb2_admin_init', 'cmb_post_type_shows_metaboxes' );
function cmb_post_type_shows_metaboxes() {

	// prefix for all custom fields
	$prefix = 'lezshows_';

	// This is just an array of all years from 1930 on (1930 being the year TV dramas started)
	$year_array = array();
	foreach ( range(date('Y'), '1930' ) as $year) {
		$startyear_array[$year] = $year;
	}

	// Must See Metabox - this should be required
	$cmb_mustsee = new_cmb2_box( array(
		'id'			=> 'mustsee_metabox',
		'title'			=> __( 'Required Details', 'lezwatchtv' ),
		'object_types'	=> array( 'post_type_shows', ), // Post type
		'context'		=> 'normal',
		'priority'		=> 'high',
		'show_names'	=> true, // Show field names on the left
	) );

	$cmb_mustsee->add_field( array(
	    'name'     => __( 'Trope Plots', 'lezwatchtv' ),
	    'id'       => $prefix . 'tropes',
		'taxonomy' => 'lez_tropes', //Enter Taxonomy Slug
		'type'     => 'taxonomy_multicheck',
		'select_all_button' => false,
		'remove_default' => 'true',
	) );

	$cmb_mustsee->add_field( array(
	    'name'    => __( 'Worth It?', 'lezwatchtv' ),
	    'id'      => $prefix . 'worthit_rating',
	    'desc'    => 'Is the show worth watching?',
	    'type'    => 'radio_inline',
	    'options' => array(
	        'Yes' => 'Yes',
	        'Meh' => 'Meh',
	        'No'  => 'No',
	    ),
	) );
	$cmb_mustsee->add_field( array(
		'name'    => __( 'Worth It Details', 'lezwatchtv' ),
		'id'      => $prefix . 'worthit_details',
		'type'    => 'textarea_small',
	) );

	// Basic Show Details
	$cmb_showdetails = new_cmb2_box( array(
		'id'			=> 'shows_metabox',
		'title'			=> __( 'Shows Details', 'lezwatchtv' ),
		'object_types'	=> array( 'post_type_shows', ), // Post type
		'context'		=> 'normal',
		'priority'		=> 'high',
		'show_names'	=> true, // Show field names on the left
	) );

	$cmb_showdetails->add_field( array(
		'name'    => __( 'Queer Timeline', 'lezwatchtv' ),
		'desc'    => __( 'Which seasons/episodes have the queer in it', 'lezwatchtv' ),
		'id'      => $prefix . 'plots',
		'type'    => 'wysiwyg',
		'options' => array( 'textarea_rows' => 10, ),
	) );

	$cmb_showdetails->add_field( array(
		'name'    => __( 'Notable Episodes', 'lezwatchtv' ),
		'desc'    => __( 'Lez-centric episodes and plotlines', 'lezwatchtv' ),
		'id'      => $prefix . 'episodes',
		'type'    => 'wysiwyg',
		'options' => array(	'textarea_rows' => 10, ),
	) );

	// Box for Ratings
	$cmb_ratings = new_cmb2_box( array(
		'id'            => 'ratings_metabox',
		'title'         => __( 'Show Rating', 'lezwatchtv' ),
		'desc'          => __( 'Ratings are subjective 1 to 5, with 1 being low and 5 being The L Word.', 'lezwatchtv' ),
		'object_types'  => array( 'post_type_shows', ), // Post type
		'context'       => 'normal',
		'priority'      => 'high',
		'show_names   ' => true, // Show field names on the left
	) );

	$cmb_ratings->add_field( array(
	    'name'    => __( 'Realness Rating', 'lezwatchtv' ),
	    'id'      => $prefix . 'realness_rating',
	    'desc'    => __( 'How realistic are the queers?', 'lezwatchtv' ),
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
		'name'    => __( 'Realness Details', 'lezwatchtv' ),
		'id'      => $prefix . 'realness_details',
		'type'    => 'wysiwyg',
		'options' => array(	'textarea_rows' => 5, ),
	) );

	$cmb_ratings->add_field( array(
	    'name'    => __( 'Show Quality Rating', 'lezwatchtv' ),
	    'id'      => $prefix . 'quality_rating',
	    'desc'    => __( 'How good is the show for queers?', 'lezwatchtv' ),
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
		'name'    => __( 'Show Quality Details', 'lezwatchtv' ),
		'id'      => $prefix . 'quality_details',
		'type'    => 'wysiwyg',
		'options' => array(	'textarea_rows' => 5, ),
	) );

	$cmb_ratings->add_field( array(
	    'name'    => __( 'Screentime Rating', 'lezwatchtv' ),
	    'id'      => $prefix . 'screentime_rating',
	    'desc'    => __( 'How much air-time do the queers get?', 'lezwatchtv' ),
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
		'name'    => __( 'Screentime Details', 'lezwatchtv' ),
		'id'      => $prefix . 'screentime_details',
		'type'    => 'wysiwyg',
		'options' => array(	'textarea_rows' => 5, ),
	) );

	// Metabox for the side (under shows)
	$cmb_notes = new_cmb2_box( array(
		'id'            	=> 'notes_metabox',
		'title'         	=> __( 'Additional Data', 'lezwatchtv' ),
		'object_types'  	=> array( 'post_type_shows', ), // Post type
		'context'       	=> 'side',
		'priority'      	=> 'default',
		'show_names'		=> true, // Show field names on the left
		'cmb_styles'		=> false,
	) );
	$cmb_notes->add_field( array(
	    'name' 				=> 'Air Dates',
	    'desc' 				=> 'Years Aired',
	    'id'   				=> $prefix . 'airdates',
		'earliest'			=> '1930',
		'text'     => array(
			'start_label'		=> '',
			'finish_label'		=> '',
		),
	    'type'				=> 'date_year_range',
	    'options'  => array(
	        'start_reverse_sort' => true,
	        'finish_reverse_sort' => true,
	    ),
	) );
	$cmb_notes->add_field( array(
	    'name'				=> 'Show Format',
	    'desc'				=> 'What kind of television entertainment is this?',
	    'id'				=> $prefix . 'tvtype',
	    'taxonomy'			=> 'lez_formats',
	    'type'				=> 'taxonomy_select',
	    'remove_default'	=> 'true',
		'default'			=> 'tvshow',
		'show_option_none'	=> false,
	) );
	$cmb_notes->add_field( array(
	    'name'				=> __( 'Show Stars', 'lezwatchtv' ),
	    'desc' 				=> __( 'Gold is by/for queers, No Stars is normal TV', 'lezwatchtv' ),
	    'id'    			=> $prefix . 'stars',
	    'type'				=> 'select',
	    'show_option_none'	=> 'No Stars',
	    'options'	 => array(
			'gold'   => 'Gold Star',
			'silver' => 'Silver Star',
	    )
	) );
	$cmb_notes->add_field( array(
	    'name' 				=> __( 'Triggers Warning?', 'lezwatchtv' ),
	    'desc' 				=> __( 'i.e. Game of Thrones, Jessica Jones, etc.', 'lezwatchtv' ),
	    'id'   				=> $prefix . 'triggerwarning',
	    'type'				=> 'checkbox'
	) );
}

/*
 * Post List Pages
 * (custom columns, quick edit, etc)
 */

// Add Custom Column Headers
add_filter( 'manage_post_type_shows_posts_columns', 'set_custom_edit_post_type_shows_columns' );
function set_custom_edit_post_type_shows_columns($columns) {
	$columns['shows-airdate']		= 'Airdates';
	$columns['shows-worthit']		= 'Worth It?';
	$columns['shows-queercount']	= '#';
	return $columns;
}

// Add Custom Column Content
add_action( 'manage_post_type_shows_posts_custom_column' , 'custom_post_type_shows_column', 10, 2 );
function custom_post_type_shows_column( $column, $post_id ) {

	if ( get_post_meta( $post_id, 'lezshows_airdates', true ) ) {
		$airdates = get_post_meta( $post_id, 'lezshows_airdates', true );
		$airdates = $airdates['start'] .' - '. $airdates['finish'];
	} else {
		$airdates = "N/A";
	}

	switch ( $column ) {
		case 'shows-airdate':
			echo $airdates;
			break;
		case 'shows-worthit':
			echo ucfirst(get_post_meta( $post_id, 'lezshows_worthit_rating', true ));
			break;
		case 'shows-queercount':
			echo get_post_meta( $post_id, 'lezshows_char_count', true );
			break;
	}
}

// Make columns sortable
add_filter( 'manage_edit-post_type_shows_sortable_columns', 'lwtv_shows_sortable_columns' );
function lwtv_shows_sortable_columns( $columns ) {
	unset( $columns['cpt-airdate'] ); 			// Don't allow sort by airdates
	$columns['shows-worthit']		= 'worth';	// Allow sort by worth
	$columns['shows-queercount']	= 'queers';	// Allow sort by queers
    return $columns;
}

// Create Worth Sortability
add_action( 'pre_get_posts', 'lwtv_shows_worth_orderby' );
function lwtv_shows_worth_orderby( $query ) {
	if( ! is_admin() ) return;

	if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
    	switch( $orderby ) {
			case 'worth':
				$query->set( 'meta_key', 'lezshows_worthit_rating' );
				$query->set( 'orderby', 'meta_value' );
				break;
			case 'queers':
				$query->set( 'meta_key', 'lezshows_char_count' );
				$query->set( 'orderby', 'meta_value_num' );
		}
	}
}

/*
 * Save post meta for shows on SHOW update
 *
 * This will update the metakey 'lezshows_char_count' on save
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
add_action( 'save_post_post_type_shows', 'lwtv_shows_update_char_count', 10, 3 );
function lwtv_shows_update_char_count( $post_id ) {

	// unhook this function so it doesn't loop infinitely
	remove_action( 'save_post_post_type_shows', 'lwtv_shows_update_char_count' );

	$meta_value = lwtv_count_queers($post_id);
	update_post_meta( $post_id, 'lezshows_char_count', $meta_value );

	// re-hook this function
	add_action( 'save_post_post_type_shows', 'lwtv_shows_update_char_count' );
}

/*
 * Save post meta for shows on CHARACTER update
 *
 * This will update the metakey 'lezshows_char_count' on save
 *
 * @param int $post_id The post ID.
 */
add_action( 'lwtv_shows_do_update_char_count', 'lwtv_shows_update_char_count', 10, 2 );
add_action( 'save_post_post_type_characters', 'lwtv_characters_update_char_count', 10, 3 );
function lwtv_characters_update_char_count( $post_id ) {

	if ( !is_array (get_post_meta( $post_id, 'lezchars_show', true)) ) {
		$shows_array = array( get_post_meta( $post_id, 'lezchars_show', true) );
	} else {
		$shows_array = get_post_meta( $post_id, 'lezchars_show', true);
	}

	foreach ( $shows_array as $show_id ) {
		do_action( 'lwtv_shows_do_update_char_count' , $show_id );
	}

}

/*
 * Count Queers
 *
 * This will update the metakey 'lezshows_char_count' on save
 *
 * @param int $post_id The post ID.
 */
function lwtv_count_queers( $post_id ) {

	// If this isn't a show post, return nothing.
	if ( get_post_type( $post_id ) !== 'post_type_shows' )
		return;

	// Loop to get the list of characters
	$charactersloop = new WP_Query(
		array(
			'post_type'       => 'post_type_characters',
			'orderby'         => 'title',
			'order'           => 'ASC',
			'posts_per_page'  => '-1',
			'meta_query'      => array(
				array(
					'key'     => 'lezchars_show',
					'value'   => $post_id,
					'compare' => 'LIKE',
				),
			),
		)
	);

	$queercount  = 0;

	// Store as array to defeat some stupid with counting and prevent querying the database too many times
	if ($charactersloop->have_posts() ) {
		while ( $charactersloop->have_posts() ) {

			$charactersloop->the_post();
			$char_id = get_the_ID();

			if ( !is_array (get_post_meta( $char_id, 'lezchars_show', true)) ) {
				$shows_array = array( get_post_meta( $char_id, 'lezchars_show', true) );
			} else {
				$shows_array = get_post_meta( $char_id, 'lezchars_show', true);
			}

			if ( in_array( $post_id, $shows_array  ) ) {
				$queercount++;
			}
		}
		wp_reset_query();
	}

	// Return Queers!
	return $queercount;
}

/*
 * AMP
 */

add_action( 'amp_init', 'lwtv_amp_add_shows_cpt' );
function lwtv_amp_add_shows_cpt() {
    add_post_type_support( 'post_type_shows', AMP_QUERY_VAR );
}
