<?php
/*
Plugin Name: CMB Field Type: Select2
Plugin URI: https://github.com/mustardBees/cmb-field-select2
Description: Select2 field type for Custom Metaboxes and Fields for WordPress
Version: 2.0.4
Author: Phil Wylie
Author URI: http://www.philwylie.co.uk/
License: GPLv2+
*/

// Useful global constants
define( 'PW_SELECT2_URL', plugin_dir_url( __FILE__ ) );
define( 'PW_SELECT2_VERSION', '2.0.4' );

/**
 * Enqueue scripts and styles, call requested select box field
 */
function pw_select2_enqueue() {

    if ( ! wp_style_is( 'cmb2-styles', 'registered' ) ) {
        return;
    }

    $js_requirements = [];

    if ( wp_script_is( 'select2' ) ) {
        $js_requirements[] = 'select2';
    }
    else {
        wp_enqueue_script( 'pw-select2-field-js', PW_SELECT2_URL . 'js/select2/select2.min.js', [ 'jquery-ui-sortable' ], '3.5.1' );
        $js_requirements[] = 'pw-select2-field-js';
    }

    wp_enqueue_script( 'pw-select2-field-init', PW_SELECT2_URL . 'js/select2-init.js', $js_requirements, PW_SELECT2_VERSION );



	$css_requirements = [];

	if ( wp_style_is( 'select2' ) ) {
		$css_requirements[] = 'select2';
	}
	else if ( wp_style_is( 'woocommerce_admin_styles' ) ) {
		$css_requirements[] = 'woocommerce_admin_styles';
	}
	else {
		wp_enqueue_style( 'pw-select2-field-css', PW_SELECT2_URL . 'js/select2/select2.css', [], '3.5.1' );
		$css_requirements[] = 'pw-select2-field-css';
	}

	wp_enqueue_style( 'pw-select2-field-mods', PW_SELECT2_URL . 'css/select2.css', $css_requirements, PW_SELECT2_VERSION );
}


/**
 * Render select box field
 */
add_filter( 'cmb2_render_pw_select', function ( $field, $value, $object_id, $object_type, $field_type_object ) {
    pw_select2_enqueue();

    if ( version_compare( CMB2_VERSION, '2.2.2', '>=' ) ) {
        $field_type_object->type = new CMB2_Type_Select( $field_type_object );
    }

	// Append an empty option (used by the placeholder)
	// $empty_option = $field->args( 'options' )[0] === '' ? '' : '<option></option>';

    echo $field_type_object->select( [
        'class'   => 'cmb2_select select2',
        'options' => $field_type_object->concat_items(),
        // Use description as placeholder
        'desc'    => $field->args( 'desc' ) && ! empty( $value ) ? $field_type_object->_desc( true ) : '',
        'data-placeholder' => esc_attr( $field->args( 'desc' ) ?: $field->args( 'name' ) ),
    ] );
}, 10, 5 );

/**
 * Render multi-value select input field
 */
add_filter( 'cmb2_render_pw_multiselect', function ( $field, $value, $object_id, $object_type, $field_type_object ) {
	pw_select2_enqueue();

    if ( version_compare( CMB2_VERSION, '2.2.2', '>=' ) ) {
        $field_type_object->type = new CMB2_Type_Select( $field_type_object );
    }

    $options = [];

    foreach ( (array) $field->options() as $opt_value => $opt_label ) {
        $options[] = [
            'id' => $opt_value,
            'text' => $opt_label
        ];
    }

    wp_localize_script( 'pw-select2-field-init', $field_type_object->_id() . '_data', $options );

    echo $field_type_object->input( [
        'type'  => 'hidden',
        'class' => 'select2',
        // Use description as placeholder
        'desc'  => $field->args( 'desc' ) && ! empty( $value ) ? $field_type_object->_desc( true ) : '',
        'data-placeholder' => esc_attr( $field->args( 'description' ) ?: $field->args( 'name' ) ),
    ] );
}, 10, 5 );


add_filter( 'cmb2_types_esc_pw_multiselect', function ( $check, $meta_value ) {
    return ! empty( $meta_value ) ? implode( ',', $meta_value ) : $check;
}, 10, 2 );

add_filter( 'cmb2_sanitize_pw_multiselect', function ( $check, $meta_value ) {

    if ( ! empty( $meta_value ) ) {
        return explode( ',', $meta_value );
    }

    return $check;
}
, 10, 2 );
