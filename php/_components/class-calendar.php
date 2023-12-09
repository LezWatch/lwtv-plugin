<?php
/**
 * Calendar Builder
 *
 * Adds custom post type for TVMaze Show Names
 */

namespace LWTV\_Components;

use Error;
use LWTV\Calendar\ICS_Parser;
use LWTV\Calendar\Names;

class Calendar implements Component, Templater {

	const POST_TYPE = 'post_type_tvmaze';

	/**
	 * Constructor
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'cmb2_init', array( $this, 'cmb2_metaboxes' ) );
		add_action( 'init', array( $this, 'create_post_type' ), 0 );

		new ICS_Parser();
		new Names();
	}

	/**
	 * Gets tags to expose as methods accessible through `lwtv_plugin()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function get_template_tags(): array {
		return array(
			'generate_ics_by_date'       => array( $this, 'generate_ics_by_date' ),
			'get_show_name_for_calendar' => array( $this, 'get_show_name_for_calendar' ),
			'download_tvmaze'            => array( $this, 'download_tvmaze' ),
			'get_tvmaze_ics'             => array( $this, 'get_tvmaze_ics' ),
		);
	}

	/**
	 * Generate what's on for a specific date
	 *
	 * @param  string $url  URL of calendar
	 * @param  string $when string of a day [today, tomorrow]
	 * @param  string $date date event happens [Y-m-d]
	 *
	 * @return array        array of all the shows on that day
	 */
	public function generate_ics_by_date( $url, $when, $date = false ) {
		return ( new ICS_Parser() )->generate_by_date( $url, $when, $date );
	}

	/**
	 * Since TV Maze sometimes uses different names than we do, we have to make a related array that can handle two names.
	 *
	 * @param string $show_name — Display Name of the show
	 * @param string $source    — lwtv or tvmaze
	 *
	 * @return string — The display name
	 */
	public function get_show_name_for_calendar( $show_name, $source = 'lwtv' ) {
		return ( new Names() )->make( $show_name, $source );
	}

	public function get_tvmaze_ics() {
		$upload_dir  = wp_upload_dir();
		$tvmaze_file = $upload_dir['basedir'] . '/tvmaze.ics';

		if ( ! file_exists( $tvmaze_file ) ) {
			return false;
		}

		return $tvmaze_file;
	}

	/**
	 * Download TV Maze
	 *
	 * Saves the ICS data to a file so we're not overloading the API.
	 *
	 * @return void
	 */
	public function download_tvmaze() {
		$ics_file = self::get_tvmaze_ics();
		$response = wp_remote_get( TV_MAZE );
		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents( $ics_file, $response['body'] );
		}
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
			'set_featured_image'       => 'Set TVMaze Name Image (recommended 1200 x 675)',
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
			'label'             => self::POST_TYPE,
			'labels'            => $labels,
			'description'       => 'TVMaze Names',
			'public'            => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'menu_position'     => 75,
			'menu_icon'         => 'dashicons-share-alt2',
			'supports'          => array( 'title' ),
			'has_archive'       => false,
			'delete_with_user'  => false,
			'capability_type'   => array( 'tvmaze_name', 'tvmaze_names' ),
			'map_meta_cap'      => true,
		);

		// Register
		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Customize Post Title Placeholder
	 * @param  string $input
	 * @return string The new title
	 */
	public function custom_enter_title( string $input ) {
		if ( self::POST_TYPE === get_post_type() ) {
			$input = 'Add TVMaze Name';
		}
		return $input;
	}

	/**
	 * CMB2 Metaboxes
	 */
	public function cmb2_metaboxes() {
		// prefix for all custom fields.
		$prefix = 'leztvmaze_';

		// MetaBox Group: Character Main Data
		$tvmaze_grid = \new_cmb2_box(
			array(
				'id'           => $prefix . 'metabox_main',
				'title'        => 'TVMaze Names',
				'object_types' => array( self::POST_TYPE ),
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
				'id'         => $prefix . 'our_show',
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
