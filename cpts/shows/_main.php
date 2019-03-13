<?php
/*
 * Custom Post Type for Shows on LWTV
 *
 * @since 1.0
 */

// Include Sub Files
require_once 'calculations.php';
require_once 'cmb2-metaboxes.php';
require_once 'custom-columns.php';
require_once 'shows-like-this.php';

/**
 * class LWTV_CPT_Shows
 */
class LWTV_CPT_Shows {

	protected static $all_taxonomies;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'create_post_type' ), 0 );
		add_action( 'init', array( $this, 'create_taxonomies' ), 0 );
		add_action( 'amp_init', array( $this, 'amp_init' ) );
		// Disabled because it's not Gutenberged yet
		// add_action( 'post_submitbox_misc_actions', array( $this, 'post_page_metabox' ) );

		// Define show taxonomies
		self::$all_taxonomies = array(
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

		// phpcs:disable
		// Hide taxonomies from Gutenberg.
		// While this isn't the official API for this need, it works.
		// https://github.com/WordPress/gutenberg/issues/6912#issuecomment-428403380
		add_filter( 'rest_prepare_taxonomy', function( $response, $taxonomy ) {
			$all_tax_array = array();
			foreach ( self::$all_taxonomies as $a_show_tax => $a_show_array ) {
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
					// Update things...
					self::update_things( $post_id );
					break;
			}
		}
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		if ( class_exists( 'VarnishPurger' ) ) {
			$this->varnish_purge = new VarnishPurger();
		}
		add_action( 'admin_head', array( $this, 'admin_css' ) );
		add_filter( 'quick_edit_show_taxonomy', array( $this, 'hide_tags_from_quick_edit' ), 10, 3 );
		add_action( 'save_post_post_type_shows', array( $this, 'save_post_meta' ), 12, 3 );
		add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
		add_filter( 'enter_title_here', array( $this, 'custom_enter_title' ) );
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
	 */
	public function create_post_type() {

		$show_taxonomies = array();
		foreach ( self::$all_taxonomies as $a_show_tax => $a_show_array ) {
			$show_taxonomies[] = $a_show_tax;
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
			'set_featured_image'       => 'Set TV Show image',
			'remove_featured_image'    => 'Remove TV Show image',
			'use_featured_image'       => 'Use as TV Show image',
			'archives'                 => 'TV Show archives',
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
				array( 'placeholder' => 'Everything we need to know about this show ...' ),
			),
		);
		$args     = array(
			'label'               => 'post_type_shows',
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
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'has_archive'         => 'shows',
			'rewrite'             => array( 'slug' => 'show' ),
			'delete_with_user'    => false,

		);
		register_post_type( 'post_type_shows', $args );
	}

	/*
	 * Create Custom Taxonomies
	 */
	public function create_taxonomies() {

		foreach ( self::$all_taxonomies as $tax_slug => $tax_details ) {
			$slug = str_replace( 'lez_', '', $tax_slug );

			$name_plural = ( isset( $tax_details['plural'] ) ) ? ucwords( $tax_details['plural'] ) : ucwords( $tax_details['name'] ) . 's';

			// Labels for taxonomy
			$labels = array(
				'name'                       => $name_plural,
				'singular_name'              => ucwords( $tax_details['name'] ),
				'search_items'               => 'Search ' . $name_plural,
				'popular_items'              => 'Popular ' . $name_plural,
				'all_items'                  => 'All' . $name_plural,
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => 'Edit ' . ucwords( $tax_details['name'] ),
				'update_item'                => 'Update ' . ucwords( $tax_details['name'] ),
				'add_new_item'               => 'Add New ' . ucwords( $tax_details['name'] ),
				'new_item_name'              => 'New' . ucwords( $tax_details['name'] ) . 'Name',
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
			// Taxonomy name
			$taxonomyname = 'lez_' . $slug;

			// Register taxonomy
			register_taxonomy( $taxonomyname, 'post_type_shows', $arguments );
		}
	}

	/**
	 * Things that have to be run when we save
	 * @param  int $post_id
	 * @return n/a - this just runs shit
	 */
	public function update_things( $post_id ) {
		// Save show scores
		LWTV_Shows_Calculate::do_the_math( $post_id );

		// Sync up data
		LWTV_CMB2_Addons::select2_taxonomy_save( $post_id, 'lezshows_tropes', 'lez_tropes' );
		LWTV_CMB2_Addons::select2_taxonomy_save( $post_id, 'lezshows_tvgenre', 'lez_genres' );
		LWTV_CMB2_Addons::select2_taxonomy_save( $post_id, 'lezshows_intersectional', 'lez_intersections' );
		LWTV_CMB2_Addons::select2_taxonomy_save( $post_id, 'lezshows_tvnations', 'lez_country' );
		LWTV_CMB2_Addons::select2_taxonomy_save( $post_id, 'lezshows_tvstations', 'lez_stations' );
	}

	/*
	 * Save post meta for shows on SHOW SAVE.
	 *
	 * @param int $post_id The post ID.
	 * @param post $post The post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 */
	public function save_post_meta( $post_id, $post, $update ) {

		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post_post_type_shows', array( $this, 'save_post_meta' ) );

		// Update Things...
		self::update_things( $post_id );

/*
		// If it's not an auto-draft, let's flush cache.
		if ( 'auto-draft' !== get_post_status( $post_id ) ) {
			// Cache Things...
			$request = wp_remote_get( get_permalink( $post_id ) . '/?nocache' );
		}
*/
		// re-hook this function
		add_action( 'save_post_post_type_shows', array( $this, 'save_post_meta' ) );
	}

	/*
	 * Add CPT to AMP
	 */
	public function amp_init() {
		add_post_type_support( 'post_type_shows', AMP_QUERY_VAR );
	}

	/*
	 * Add to 'Right Now'
	 */
	public function dashboard_glance_items() {
		foreach ( array( 'post_type_shows' ) as $post_type ) {
			$num_posts = wp_count_posts( $post_type );
			if ( $num_posts && $num_posts->publish ) {
				if ( 'post_type_shows' === $post_type ) {
					// translators: %s is the number of TV shows we have (total)
					$text = _n( '%s TV Show', '%s TV Shows', $num_posts->publish );
				}
				$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );
				printf( '<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', esc_attr( $post_type ), esc_html( $text ) );
			}
		}
	}

	/*
	 * Style for dashboard
	 */
	public function admin_css() {
		echo "<style type='text/css'>
			#adminmenu #menu-posts-post_type_shows div.wp-menu-image:before, #dashboard_right_now li.post_type_shows-count a:before {
				content: '\\f126';
				margin-left: -1px;
			}
			select#lezshows_airdates_start, select#lezshows_airdates_finish {
				width: 40%;
			}
		</style>";
	}

	/*
	 * Post Page Meta Box
	 * For listing critical information
	 */
	public function post_page_metabox() {
		global $post;

		switch ( $post->post_type ) {
			case 'post_type_shows':
				$countqueers = get_post_meta( $post->ID, 'lezshows_char_count', true );
				$deadqueers  = get_post_meta( $post->ID, 'lezshows_dead_count', true );
				$score       = get_post_meta( $post->ID, 'lezshows_the_score', true );
				$loved       = ( 'on' === get_post_meta( $post->ID, 'lezshows_worthit_show_we_love', true ) ) ? 'Yes' : 'No';
				$short_score = round( $score, 2 );

				echo '<div class="misc-pub-section lwtv misc-pub-lwtv">
					<span id="loved">Loved: <b>' . esc_html( $loved ) . '</b></span>
				</div>
				<div class="misc-pub-section lwtv misc-pub-lwtv">
					<span id="characters">Characters: <b>' . (int) $countqueers . '</b> total';
				if ( $deadqueers ) {
					echo '/ <b>' . (int) $deadqueers . '</b> dead';
				}
				echo '</span>
				</div>
				<div class="misc-pub-section lwtv misc-pub-lwtv">
					<span id="score">Score: <b>' . floatval( $short_score ) . '</b></span>
				</div>';

				break;
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
		if ( 'post_type_shows' === get_post_type() ) {
			$input = 'Add show';
		}
		return $input;
	}

}

new LWTV_CPT_Shows();
