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

/**
 * Extra Get post options.
 */
function cmb2_get_post_options( $query_args ) {

    $args = wp_parse_args( $query_args, array(
        'post_type'   => 'post',
        'numberposts' => -1,
        'post_status' => array('publish'),
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

/**
 * CSS tweaks
 */
add_action( 'admin_enqueue_scripts', 'cmb2_lez_scripts', 10 );
function cmb2_lez_scripts( $hook ) {
	if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'edit-tags.php' || $hook == 'term.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {
		wp_register_style( 'cmb-styles', plugins_url('/cmb2.css', __FILE__ ) );
		wp_enqueue_style( 'cmb-styles' );
	}
}

/**
 * Hook in and add a metabox to add fields to taxonomy terms
 */
add_action( 'cmb2_admin_init', 'lezwatch_register_taxonomy_metabox' );
function lezwatch_register_taxonomy_metabox() {
	$prefix = 'lez_termsmeta_';

	$icon_array = array();
	foreach (glob( get_stylesheet_directory().'/images/symbolicons/*.svg' ) as $file) {
		$icon_array[ basename($file, '.svg') ] = basename($file, '.svg');
	}

	/**
	 * Metabox to add fields to categories and tags
	 */
	$cmb_term = new_cmb2_box( array(
		'id'               => $prefix . 'edit',
		'title'            => __( 'Category Metabox', 'cmb2' ), // Doesn't output for term boxes
		'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
		'taxonomies'       => array( 'lez_cliches', 'lez_chartags' ), // Tells CMB2 which taxonomies should have these fields
		'new_term_section' => true, // Will display in the "Add New Category" section
	) );

	$cmb_term->add_field( array(
		'name'			=> __( 'Icon', 'cmb2' ),
		'desc'			=> __( 'Name of the image you want to use', 'cmb2' ),
		'id'			=> $prefix . 'icon',
		//'type'			=> 'text_medium',
	    'type'             => 'select',
	    'show_option_none' => true,
	    'default'          => 'custom',
	    'options'          => $icon_array,
		'before_field'	=> 'lez_before_field_icon',
	) );
}

/**
 * Show an icon if that exists
 *
 * @param  array             $field_args Array of field parameters
 * @param  CMB2_Field object $field      Field object
 */
function lez_before_field_icon( $field_args, $field ) {

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);


	$icon = $field->value;
	$iconpath = get_stylesheet_directory().'/images/symbolicons/'.$icon.'.svg';
	if ( !empty($icon) || file_exists( $iconpath ) ) {
		echo '<span role="img" class="cmb2-icon">'.file_get_contents(get_stylesheet_directory_uri().'/images/symbolicons/'.$icon.'.svg').'</span>';
	}
}

add_filter( "manage_edit-lez_cliches_columns", "lez_terms_column_header_function" );
add_action( "manage_lez_cliches_custom_column",  "lez_terms_populate_rows_function", 10, 3  );
add_filter( "manage_edit-lez_chartags_columns", "lez_terms_column_header_function" );
add_action( "manage_lez_chartags_custom_column",  "lez_terms_populate_rows_function", 10, 3  );


function lez_terms_column_header_function($columns){
    $columns['icon'] = 'Icon';
    return $columns;
}

function lez_terms_populate_rows_function($value, $content, $term_id){
	$icon = get_term_meta( $term_id, 'lez_termsmeta_icon', true );
	$iconpath = get_stylesheet_directory().'/images/symbolicons/'.$icon.'.svg';
	if ( empty($icon) || !file_exists( $iconpath ) ) {
		$content = 'N/A';
	} else {
		$content = '<span role="img" class="cmb2-icon">'.file_get_contents($iconpath).'</span>';
	}
    return $content;
}