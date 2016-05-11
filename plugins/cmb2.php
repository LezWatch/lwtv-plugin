<?php
/*
Description: Customizations for CMB2
Version: 1.0
Author: Mika Epstein
*/

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
	wp_register_style( 'cmb-styles', plugins_url('cmb2.css', __FILE__ ) );
	if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'edit-tags.php' || $hook == 'term.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {
		wp_enqueue_style( 'cmb-styles' );
	}
}

/**
 * Add metabox to custom taxonomies to show icon
 *
 * $icon_taxonomies   array of taxonomies to show icons on.
 * $symbolicon_path   location of Symbolicons
 *
 * lez_register_taxonomy_metabox()   CMB2 mextabox code
 *
 * lez_before_field_icon()    Show an icon if that exists
 * @param  array              $field_args  Array of field parameters
 * @param  CMB2_Field object  $field       Field object
 *
 * lez_add_taxonomy_icon_options()      how column if taxonomy is in $icon_taxonomies
 * lez_terms_column_header_function()   Column header
 * lez_terms_populate_rows_function()   Column content
 */

$icon_taxonomies = array( 'lez_cliches', 'lez_chartags', 'lez_gender', 'lez_sexuality' );

$symbolicon_path = get_stylesheet_directory().'/images/symbolicons/';

// Only load the icons IF the icon folder is there. This will prevent weird theme switching errors
if ( file_exists( $symbolicon_path ) && is_dir( $symbolicon_path ) ) {

	// Add CMB2 Metabox
	add_action( 'cmb2_admin_init', 'lez_register_taxonomy_metabox' );
	function lez_register_taxonomy_metabox() {
		global $icon_taxonomies, $symbolicon_path;
		$prefix = 'lez_termsmeta_';

		$icon_array = array();
		foreach (glob( $symbolicon_path.'*.svg' ) as $file) {
			$icon_array[ basename($file, '.svg') ] = basename($file);
		}

		$symbolicon_url = admin_url( 'themes.php?page=symbolicons' );

		$cmb_term = new_cmb2_box( array(
			'id'				=> $prefix . 'edit',
			'title'				=> 'Category Metabox',
			'object_types'		=> array( 'term' ),
			'taxonomies'		=> $icon_taxonomies,
			'new_term_section'	=> true,
		) );

		$cmb_term->add_field( array(
			'name'				=> 'Icon',
			'desc'				=> 'Select the icon you want to use. Once saved, it will show on the left.<br />If you need help visualizing, check out the <a href='.$symbolicon_url.'>Symbolicons List</a>.',
			'id'				=> $prefix . 'icon',
		    'type'				=> 'select',
		    'show_option_none'	=> true,
		    'default'			=> 'custom',
		    'options'			=> $icon_array,
			'before_field'		=> 'lez_before_field_icon',
		) );
	}

	// Add before field icon display
	function lez_before_field_icon( $field_args, $field ) {
		global $symbolicon_path;

		$icon = $field->value;
		$iconpath = $symbolicon_path.$icon.'.svg';
		if ( !empty($icon) || file_exists( $iconpath ) ) {
			echo '<span role="img" class="cmb2-icon">'.file_get_contents( $iconpath ).'</span>';
		}
	}

	// Add all filters and actions to show icons on tax list page
	foreach ( $icon_taxonomies as $tax_name ) {
		add_filter( 'manage_edit-'.$tax_name. '_columns',  'lez_terms_column_header' );
		add_action( 'manage_'.$tax_name. '_custom_column', 'lez_terms_column_content', 10, 3 );
	}

	// Tax list column header
	function lez_terms_column_header($columns){
	    $columns['icon'] = 'Icon';
	    return $columns;
	}

	// Tax list column content
	function lez_terms_column_content($value, $content, $term_id){
		global $symbolicon_path;
		$icon = get_term_meta( $term_id, 'lez_termsmeta_icon', true );
		$iconpath = $symbolicon_path.$icon.'.svg';
		if ( empty($icon) || !file_exists( $iconpath ) ) {
			$content = 'N/A';
		} else {
			$content = '<span role="img" class="cmb2-icon">'.file_get_contents($iconpath).'</span>';
		}
	    return $content;
	}
}