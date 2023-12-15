<?php
/*
 * Dashboard: Custom Dashboard and WP Admin Changes
 *
 * @version 1.2.1
 * @package library
 */

namespace LWTV\Features;

class Dashboard {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'manage_posts_columns', array( $this, 'featured_image_manage_posts_columns' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'featured_image_manage_custom_columns' ), 10, 2 );
	}

	/*
	 * Create custom column for featured images in posts lists
	 *
	 * @since 1.1
	 */
	public function featured_image_manage_posts_columns( $columns ) {
		if ( ! is_array( $columns ) ) {
			$columns = array();
		}
		$new_columns = array();

		// Put the feature image right after title
		foreach ( $columns as $key => $title ) {
			$new_columns[ $key ] = $title;
			if ( 'title' === $key ) {
				$new_columns['featured_image'] = '<span class="dashicons dashicons-camera"><span class="screen-reader-text">Image?</span></span>';
			}
		}

		return $new_columns;
	}

	/*
	 * Content for featured image custom column
	 *
	 * @since 1.1
	 */
	public function featured_image_manage_custom_columns( $column_name, $post_ID ) {
		if ( 'featured_image' !== $column_name ) {
			return;
		}

		$post_type = get_post_type( $post_ID );

		// Don't run for TV Maze
		if ( 'post_type_tvmaze' === $post_type ) {
			return;
		}

		$mystery_array     = array( 10250, 11066, 79739, 87052 );
		$post_thumbnail_id = get_post_thumbnail_id( $post_ID );
		$output            = '<span class="dashicons dashicons-no"><span class="screen-reader-text">No</span></span>';
		if ( $post_thumbnail_id ) {
			if ( in_array( $post_thumbnail_id, $mystery_array, true ) ) {
				$output = '<span class="dashicons dashicons-flag"><span class="screen-reader-text">?</span></span>';
			} else {
				$output = '<span class="dashicons dashicons-yes"><span class="screen-reader-text">Yes</span></span>';
			}
		}

		echo wp_kses_post( $output );
	}
}
