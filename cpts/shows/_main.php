<?php
/*
 * Custom Post Type for Shows on LWTV
 *
 * @since 1.0
 */

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
			'lez_stations'      => array(
				'name' => 'TV station',
				'rest' => false,
			),
			'lez_tropes'        => array(
				'name' => 'trope',
				'rest' => false,
			),
			'lez_formats'       => array(
				'name' => 'format',
				'rest' => false,
			),
			'lez_genres'        => array(
				'name' => 'genre',
				'rest' => false,
			),
			'lez_country'       => array(
				'name' => 'nation',
				'rest' => false,
			),
			'lez_stars'         => array(
				'name' => 'star',
				'rest' => false,
			),
			'lez_triggers'      => array(
				'name' => 'trigger',
				'rest' => false,
			),
			'lez_intersections' => array(
				'name' => 'intersection',
				'rest' => false,
			),
			'lez_showtagged'    => array(
				'name'   => 'tagged',
				'plural' => 'tagged',
				'rest'   => true,
			),
		);
	}

	/**
	 *  Init
	 */
	public function init() {
		// Things that only run for this post type
		$post_id = ( isset( $_GET['post'] ) ) ? intval( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		if ( 0 !== $post_id ) {
			$post_type = ( isset( $_GET['post_type'] ) ) ? sanitize_text_field( $_GET['post_type'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
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
		add_filter( 'manage_post_type_shows_posts_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_post_type_shows_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'manage_edit-post_type_shows_sortable_columns', array( $this, 'manage_edit_sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'columns_sortability_simple' ) );
		add_filter( 'posts_clauses', array( $this, 'columns_sortability_format' ), 10, 2 );
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

		$labels = array(
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
		$args   = array(
			'label'               => 'post_type_shows',
			'labels'              => $labels,
			'description'         => 'TV Shows',
			'public'              => true,
			'exclude_from_search' => false,
			'show_in_rest'        => true,
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
				'show_in_rest'          => $tax_details['rest'],
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

	/*
	 * Create Custom Columns
	 * Used by quick edit, etc
	 */
	public function manage_posts_columns( $columns ) {
		$columns['shows-airdate']    = 'Airdates';
		$columns['shows-worthit']    = 'Worth It?';
		$columns['shows-queercount'] = '#';
		return $columns;
	}

	/*
	 * Add Custom Column Content
	 */
	public function manage_posts_custom_column( $column, $post_id ) {
		if ( get_post_meta( $post_id, 'lezshows_airdates', true ) ) {
			$airdates = get_post_meta( $post_id, 'lezshows_airdates', true );
			$airdate  = $airdates['start'] . ' - ' . $airdates['finish'];
			if ( $airdates['start'] === $airdates['finish'] ) {
				$airdate = $airdates['finish'];
			}
		} else {
			$airdate = 'N/A';
		}

		switch ( $column ) {
			case 'shows-airdate':
				$output = $airdate;
				break;
			case 'shows-worthit':
				$output = ucfirst( get_post_meta( $post_id, 'lezshows_worthit_rating', true ) );
				break;
			case 'shows-queercount':
				$output = get_post_meta( $post_id, 'lezshows_char_count', true );
				break;
			default:
				$output = '';
		}

		echo wp_kses_post( $output );
	}

	/*
	 * Make Custom Columns Sortable
	 */
	public function manage_edit_sortable_columns( $columns ) {
		unset( $columns['cpt-airdate'] );             // Don't allow sort by airdates
		$columns['taxonomy-lez_formats'] = 'format';  // Allow sort by show format
		$columns['shows-worthit']        = 'worth';   // Allow sort by worth
		$columns['shows-queercount']     = 'queers';  // Allow sort by queers
		return $columns;
	}

	/*
	 * Create Simple Columns Sortability
	 *
	 * Worth It, Char Count
	 */
	public function columns_sortability_simple( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		if ( $query->is_main_query() && isset( $orderby ) && $orderby === $query->get( 'orderby' ) ) {
			switch ( $orderby ) {
				case 'worth':
					$query->set( 'meta_key', 'lezshows_worthit_rating' );
					$query->set( 'orderby', 'meta_value' );
					break;
				case 'queers':
					$query->set( 'meta_key', 'lezshows_char_count' );
					$query->set( 'orderby', 'meta_value_num' );
					break;
			}
		}
	}

	/*
	 * Create columns sortability for show Formats
	 */
	public function columns_sortability_format( $clauses, $wp_query ) {
		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'format' === $wp_query->query['orderby'] ) {

			$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

			$clauses['where']   .= " AND (taxonomy = 'lez_formats' OR taxonomy IS NULL)";
			$clauses['groupby']  = 'object_id';
			$clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
			$clauses['orderby'] .= ( 'ASC' === strtoupper( $wp_query->get( 'order' ) ) ) ? 'ASC' : 'DESC';
		}

		return $clauses;
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

		// If it's not an auto-draft, let's flush cache.
		if ( ! ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) ) {
			// Cache Things...
			$request = wp_remote_get( get_permalink( $post_id ) . '/?nocache' );
		}

		// re-hook this function
		add_action( 'save_post_post_type_shows', array( $this, 'save_post_meta' ) );
	}

	/*
	 * Save post meta for shows on CHARACTER update
	 *
	 * This will update the metakey 'lezshows_char_count' on save
	 *
	 * @param int $post_id The post ID.
	 */
	public function update_meta_from_chars( $post_id ) {

		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post_post_type_characters', array( $this, 'update_meta_from_chars' ) );

		$screen   = get_current_screen();
		$show_ids = get_post_meta( $post_id, 'lezchars_show_group', true );

		if ( '' !== $show_ids ) {
			foreach ( $show_ids as $each_show ) {
				LWTV_Shows_Calculate::do_the_math( $each_show['show'] );
				self::flush_varnish( $each_show['show'], $screen );
			}
		}

		// re-hook this function
		add_action( 'save_post_post_type_characters', array( $this, 'update_meta_from_chars' ) );
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

// Include Sub Files
require_once 'calculations.php';
require_once 'cmb2-metaboxes.php';
require_once 'shows-like-this.php';

new LWTV_CPT_Shows();
