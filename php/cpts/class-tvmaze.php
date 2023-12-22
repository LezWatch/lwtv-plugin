<?php
/*
 * Custom Post Type for TVMaze on LWTV
 *
 * @since 1.0
 */

namespace LWTV\CPTs;

use LWTV\CPTs\Shows\CMB2_Metaboxes;

class TVMaze {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const SLUG = 'post_type_tvmaze';

	/**
	 * CMB2 Prefix
	 */
	const PREFIX = 'leztvmaze_';

	/**
	 * Constructor
	 */
	public function __construct() {
		new CMB2_Metaboxes();
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'cmb2_init', array( $this, 'cmb2_metaboxes' ) );
		add_action( 'init', array( $this, 'create_post_type' ), 0 );
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		add_filter( 'enter_title_here', array( $this, 'custom_enter_title' ) );
	}

	/**
	 * Create Custom Post Type
	 */
	public function create_post_type() {
		$labels = array(
			'name'                     => 'TVMaze Names',
			'singular_name'            => 'TVMaze Name',
			'menu_name'                => 'TVMaze Names',
			'add_new_item'             => 'Add New TVMaze Name',
			'edit_item'                => 'Edit TVMaze Name',
			'new_item'                 => 'New TVMaze Name',
			'view_item'                => 'View TVMaze Name',
			'all_items'                => 'All TVMaze Names',
			'search_items'             => 'Search TVMaze Names',
			'not_found'                => 'No TVMaze Names found',
			'not_found_in_trash'       => 'No TVMaze Names found in Trash',
			'update_item'              => 'Update TVMaze Name',
			'featured_image'           => 'TVMaze Name Image',
			'set_featured_image'       => 'Set TVMaze Name Image',
			'remove_featured_image'    => 'Remove TVMaze Name Image',
			'use_featured_image'       => 'Use as TVMaze Name Image',
			'archives'                 => 'TVMaze Name Archives',
			'insert_into_item'         => 'Insert into TVMaze Name',
			'uploaded_to_this_item'    => 'Uploaded to this TVMaze Name',
			'filter_items_list'        => 'Filter TVMaze Name list',
			'items_list_navigation'    => 'TVMaze Name list navigation',
			'items_list'               => 'TVMaze Name list',
			'item_published'           => 'TVMaze Name published.',
			'item_published_privately' => 'TVMaze Name published privately.',
			'item_reverted_to_draft'   => 'TVMaze Name reverted to draft.',
			'item_scheduled'           => 'TVMaze Name scheduled.',
			'item_updated'             => 'TVMaze Name updated.',
		);
		$args   = array(
			'label'             => self::SLUG,
			'labels'            => $labels,
			'description'       => 'TVMaze Names',
			'public'            => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'menu_position'     => 75,
			'menu_icon'         => 'dashicons-share-alt2',
			'supports'          => array( 'title', 'custom-fields' ),
			'has_archive'       => false,
			'delete_with_user'  => false,
			'capability_type'   => array( 'tvmaze_name', 'tvmaze_names' ),
			'map_meta_cap'      => true,
		);

		// Register
		register_post_type( self::SLUG, $args );
	}

	/**
	 * Customize Post Title Placeholder
	 *
	 * @param  string $input
	 * @return string The new title
	 */
	public function custom_enter_title( string $input ) {
		if ( self::SLUG === get_post_type() ) {
			$input = 'Add TVMaze Name';
		}
		return $input;
	}

	/**
	 * CMB2 Metaboxes
	 */
	public function cmb2_metaboxes() {

		// MetaBox Group: Character Main Data
		$tvmaze_grid = \new_cmb2_box(
			array(
				'id'           => self::PREFIX . 'metabox_main',
				'title'        => 'TVMaze Names',
				'object_types' => array( self::SLUG ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_in_rest' => false,
				'show_names'   => true, // Show field names on the left
			)
		);

		// Field: Show
		$tvmaze_grid->add_field(
			array(
				'name'       => 'TV Show',
				'id'         => self::PREFIX . 'our_show',
				'desc'       => 'Select one show. Each TVMaze show can only link to ONE show.',
				'type'       => 'custom_attached_posts', // This field type
				'post_type'  => 'post_type_shows',
				'options'    => array(
					'query_args' => array(
						'posts_per_page' => 5,
						'post_type'      => 'post_type_shows',
					), // override the get_posts args
				),
				'attributes' => array(
					'data-max-items' => 1,
				),
			)
		);
	}
}
