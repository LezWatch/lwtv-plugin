<?php
/*
 * Customize Featured images for CPTs
 *
 * @since 1.5
 * Authors: Mika Epstein
 */


/**
 * Rename Featured Images
 */
add_action( 'admin_init', 'lez_featured_images' );
function lez_featured_images() {
	$post_type_args = array(
	   'public'   => true,
	   '_builtin' => false
	);
	$post_types = get_post_types( $post_type_args, 'objects' );
	foreach ( $post_types as $post_type ) {

		$type = $post_type->name;
		$name = $post_type->labels->singular_name;

		// change the default "Featured Image" metabox title
		add_action('do_meta_boxes', function() use ( $type, $name ) {
			remove_meta_box( 'postimagediv', $type, 'side' );
			add_meta_box('postimagediv', $name.' Image', 'post_thumbnail_meta_box', $type, 'side');
		});

		// change the default "Set Featured Image" text
		add_filter( 'admin_post_thumbnail_html', function( $content ) use ( $type, $name ) {
			global $current_screen;
			if( !is_null($current_screen) && $type == $current_screen->post_type ) {
			    // Get featured image size
			    global $_wp_additional_image_sizes;
			    $genesis_image_size = rtrim( str_replace( 'post_type_', '', $type ), 's' ).'-img';
			    if ( isset( $_wp_additional_image_sizes[ $genesis_image_size ] ) ) {
			        $content = '<p>Image Size: ' . $_wp_additional_image_sizes[$genesis_image_size]['width'] . 'x' . $_wp_additional_image_sizes[$genesis_image_size]['height'] . 'px</p>' . $content;
			    }
				$content = str_replace( __( 'featured' ), strtolower( $name ) , $content);
			}
			return $content;
		});
	}
}