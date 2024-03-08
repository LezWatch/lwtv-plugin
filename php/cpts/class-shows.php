<?php
/*
 * Custom Post Type for Shows on LWTV
 *
 * @since 1.0
 */

namespace LWTV\CPTs;

use LWTV\CPTs\Shows\Calculations;
use LWTV\CPTs\Shows\CMB2_Metaboxes;
use LWTV\CPTs\Shows\Custom_Columns;
use LWTV\CPTs\Shows\Shows_Like_This;

/**
 * class LWTV_CPT_Shows
 */
class Shows {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const SLUG = 'post_type_shows';

	/**
	 * All Taxonomies
	 *
	 * @var array
	 */
	const ALL_TAXONOMIES = array(
		'lez_stations'      => array( 'name' => 'TV station' ),
		'lez_tropes'        => array( 'name' => 'trope' ),
		'lez_formats'       => array( 'name' => 'format' ),
		'lez_genres'        => array( 'name' => 'genre' ),
		'lez_country'       => array( 'name' => 'nation' ),
		'lez_stars'         => array( 'name' => 'star' ),
		'lez_triggers'      => array( 'name' => 'trigger' ),
		'lez_intersections' => array( 'name' => 'intersection' ),
		'lez_showtagged'    => array(
			'name'   => 'tagged',
			'plural' => 'tagged',
			'hide'   => false,
		),
	);

	/**
	 * Taxonomies that use Select2
	 */
	const SELECT2_TAXONOMIES = array(
		'lezshows_tropes'         => 'lez_tropes',
		'lezshows_tvgenre'        => 'lez_genres',
		'lezshows_intersectional' => 'lez_intersections',
		'lezshows_tvnations'      => 'lez_country',
		'lezshows_tvstations'     => 'lez_stations',
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		new CMB2_Metaboxes();
		new Custom_Columns();
		new Shows_Like_This();

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'init' ) );

		// Create CPT and Taxes
		add_action( 'init', array( $this, 'create_post_type' ), 0 );
		add_action( 'init', array( $this, 'create_taxonomies' ), 0 );

		// phpcs:disable
		// Hide taxonomies from Gutenberg.
		// While this isn't the official API for this need, it works.
		// https://github.com/WordPress/gutenberg/issues/6912#issuecomment-428403380
		add_filter( 'rest_prepare_taxonomy', function( $response, $taxonomy ) {
			$all_tax_array = array();
			foreach ( self::ALL_TAXONOMIES as $a_show_tax => $a_show_array ) {
				if ( ! isset( $a_show_array['hide'] ) || false !== $a_show_array['hide'] ) {
					$all_tax_array[] = $a_show_tax;
				}
			}
			if ( in_array( $taxonomy->name, $all_tax_array ) ) {
				$response->data['visibility']['show_ui'] = false;
			}
			return $response;
		}, 10, 2 );
		// phpcs:enable
	}

	/**
	 *  Init
	 */
	public function init() {
		// Things that only run for this post type
		// phpcs:ignore WordPress.Security.NonceVerification
		$post_id = ( isset( $_GET['post'] ) ) ? intval( $_GET['post'] ) : 0;
		if ( 0 !== $post_id ) {
			// phpcs:ignore WordPress.Security.NonceVerification
			$post_type = ( isset( $_GET['post_type'] ) ) ? sanitize_text_field( $_GET['post_type'] ) : 0;
			switch ( $post_type ) {
				case 'post_type_shows':
					if ( is_admin() ) {
						// Filter buttons not needed on the teeny MCE
						add_filter( 'teeny_mce_buttons', array( $this, 'teeny_mce_buttons' ) );
					}
					break;
			}
		}
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		add_filter( 'quick_edit_show_taxonomy', array( $this, 'hide_tags_from_quick_edit' ), 10, 3 );
		add_action( 'save_post_post_type_shows', array( $this, 'save_post_meta' ), 12, 3 );
		add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
		add_filter( 'enter_title_here', array( $this, 'custom_enter_title' ) );
	}

	/**
	 * Do The Math
	 *
	 * @param  int  $post_id
	 * @return void
	 */
	public function do_the_math( $show_id ) {
		( new Calculations() )->do_the_math( $show_id );
	}

	/**
	 * Remove some Text Editor buttons
	 */
	public function quicktags_settings( $buttons ) {
		$remove             = array( 'del', 'ins', 'img', 'code', 'block' );
		$buttons['buttons'] = implode( ',', array_diff( explode( ',', $buttons['buttons'] ), $remove ) );
		return $buttons;
	}

	/**
	 * Remove some TEENY MCE buttons (not TinyMCE, TeenyMCE)
	 */
	public function teeny_mce_buttons( $buttons ) {
		$remove = array( 'alignleft', 'aligncenter', 'alignright', 'undo', 'redo', 'fullscreen' );
		return array_diff( $buttons, $remove );
	}

	/**
	 * Create Custom Post Type
	 *
	 * post_type_shows
	 */
	public function create_post_type() {

		$show_taxonomies = array();
		foreach ( self::ALL_TAXONOMIES as $show_tax => $show_array ) {
			$show_taxonomies[] = $show_tax;
		}

		$labels   = array(
			'name'                     => 'TV Shows',
			'singular_name'            => 'TV Show',
			'menu_name'                => 'TV Shows',
			'add_new_item'             => 'Add New TV Show',
			'edit_item'                => 'Edit TV Show',
			'new_item'                 => 'New TV Show',
			'view_item'                => 'View TV Show',
			'all_items'                => 'All TV Shows',
			'search_items'             => 'Search TV Shows',
			'not_found'                => 'No TV Shows found',
			'not_found_in_trash'       => 'No TV Shows found in Trash',
			'update_item'              => 'Update TV Show',
			'featured_image'           => 'TV Show Image',
			'set_featured_image'       => 'Set TV Show Image (recommended 1200 x 675)',
			'remove_featured_image'    => 'Remove TV Show Image',
			'use_featured_image'       => 'Use as TV Show Image',
			'archives'                 => 'TV Show Archives',
			'insert_into_item'         => 'Insert into TV Show',
			'uploaded_to_this_item'    => 'Uploaded to this TV Show',
			'filter_items_list'        => 'Filter TV Show list',
			'items_list_navigation'    => 'TV Show list navigation',
			'items_list'               => 'TV Show list',
			'item_published'           => 'TV Show published.',
			'item_published_privately' => 'TV Show published privately.',
			'item_reverted_to_draft'   => 'TV Show reverted to draft.',
			'item_scheduled'           => 'TV Show scheduled.',
			'item_updated'             => 'TV Show updated.',
		);
		$template = array(
			array( 'lez-library/featured-image' ),
			array(
				'core/paragraph',
				array( 'placeholder' => 'Everything we need to know about this show.' ),
			),
		);
		$args     = array(
			'label'               => self::SLUG,
			'labels'              => $labels,
			'description'         => 'TV Shows',
			'public'              => true,
			'exclude_from_search' => false,
			'show_in_rest'        => true,
			'template'            => $template,
			'taxonomies'          => $show_taxonomies,
			'rest_base'           => 'show',
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-video-alt',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
			'has_archive'         => 'shows',
			'rewrite'             => array( 'slug' => 'show' ),
			'delete_with_user'    => false,
			'capability_type'     => array( 'show', 'shows' ),
			'map_meta_cap'        => true,
		);
		register_post_type( self::SLUG, $args );
	}

	/*
	 * Create Custom Taxonomies
	 */
	public function create_taxonomies() {

		foreach ( self::ALL_TAXONOMIES as $tax_slug => $tax_details ) {
			$slug = str_replace( 'lez_', '', $tax_slug );

			$name_singular = ucwords( $tax_details['name'] );
			$name_plural   = ( isset( $tax_details['plural'] ) ) ? ucwords( $tax_details['plural'] ) : ucwords( $tax_details['name'] ) . 's';

			// Labels for taxonomy
			$labels = array(
				'name'                       => $name_plural,
				'singular_name'              => $name_singular,
				'search_items'               => 'Search ' . $name_plural,
				'popular_items'              => 'Popular ' . $name_plural,
				'all_items'                  => 'All' . $name_plural,
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => 'Edit ' . $name_singular,
				'update_item'                => 'Update ' . $name_singular,
				'add_new_item'               => 'Add New ' . $name_singular,
				'new_item_name'              => 'New' . $name_singular . 'Name',
				'separate_items_with_commas' => 'Separate ' . $name_plural . ' with commas',
				'add_or_remove_items'        => 'Add or remove' . $name_plural,
				'choose_from_most_used'      => 'Choose from the most used ' . $name_plural,
				'not_found'                  => 'No ' . $name_plural . ' found.',
				'menu_name'                  => $name_plural,
			);

			//parameters for the new taxonomy
			$arguments = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_in_rest'          => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'show_in_nav_menus'     => true,
				'rest_base'             => rtrim( $slug, 's' ),
				'rest_controller_class' => 'WP_REST_Terms_Controller',
				'rewrite'               => array( 'slug' => rtrim( $slug, 's' ) ),
			);

			// Register taxonomy
			register_taxonomy( $tax_slug, self::SLUG, $arguments );
		}
	}

	/*
	 * Save post meta for shows on SHOW SAVE.
	 *
	 * @param int $post_id The post ID.
	 * @param post $post The post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	public function save_post_meta( $post_id, $post, $update ) {

		// Prevent running on autosave.
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return;
		}

		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post_post_type_shows', array( $this, 'save_post_meta' ) );

		// Save show scores
		lwtv_plugin()->calculate_show_data( $post_id );

		// ALWAYS sync up data.
		foreach ( self::SELECT2_TAXONOMIES as $postmeta => $taxonomy ) {
			lwtv_plugin()->save_select2_taxonomy( $post_id, $postmeta, $taxonomy );
		}

		// re-hook this function
		add_action( 'save_post_post_type_shows', array( $this, 'save_post_meta' ) );
	}

	/*
	 * Add to 'Right Now'
	 */
	public function dashboard_glance_items() {
		foreach ( array( self::SLUG ) as $post_type ) {
			$num_posts = wp_count_posts( $post_type );
			if ( $num_posts && $num_posts->publish ) {
				if ( self::SLUG === $post_type ) {
					// translators: %s is the number of TV shows we have (total)
					$text = _n( '%s TV Show', '%s TV Shows', $num_posts->publish );
				}
				$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );
				printf( '<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', esc_attr( $post_type ), esc_html( $text ) );
			}
		}
	}

	/**
	 * Hide taxonomies from quick edit
	 *
	 * @access public
	 * @param mixed $show_in_quick_edit
	 * @param mixed $taxonomy_name
	 * @param mixed $post_type
	 * @return void
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	public function hide_tags_from_quick_edit( $show_in_quick_edit, $taxonomy_name, $post_type ) {
		$taxonomies = array( 'lez_tropes', 'lez_formats', 'lez_genres', 'lez_stars', 'lez_triggers', 'lez_intersections' );
		if ( in_array( $taxonomy_name, $taxonomies, true ) ) {
			return false;
		} else {
			return $show_in_quick_edit;
		}
	}

	/**
	 * Customize Post Title Placeholder
	 * @param  string $input
	 * @return string The new title
	 */
	public function custom_enter_title( $input ) {
		if ( self::SLUG === get_post_type() ) {
			$input = 'Add show';
		}
		return $input;
	}
}
