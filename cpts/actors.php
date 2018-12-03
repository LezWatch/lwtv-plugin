<?php
/*
 * Custom Post Type for actors on LWTV
 *
 * @since 1.0
 */

/**
 * class LWTV_CPT_Actors
 */
class LWTV_CPT_Actors {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'create_post_type' ), 0 );
		add_action( 'init', array( $this, 'create_taxonomies' ), 0 );
		add_action( 'amp_init', array( $this, 'amp_init' ) );
		add_action( 'wpseo_register_extra_replacements', array( $this, 'yoast_seo_register_extra_replacements' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_page_metabox' ) );
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		add_action( 'admin_head', array( $this, 'admin_css' ) );
		add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
		add_action( 'do_update_actor_meta', array( $this, 'update_meta' ), 10, 2 );
		add_action( 'save_post_post_type_characters', array( $this, 'update_meta_from_chars' ), 10, 3 );
		add_filter( 'manage_post_type_actors_posts_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_post_type_actors_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'manage_edit-post_type_actors_sortable_columns', array( $this, 'manage_edit_sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'columns_sortability_simple' ) );

	}

	/**
	 *  Init
	 */
	public function init() {
		// Things that only run for this post type
		$post_id = ( isset( $_GET['post'] ) ) ? intval( $_GET['post'] ) : 0;  // WPCS: CSRF ok.
		if ( 0 !== $post_id && is_admin() ) {
			$post_type = ( isset( $_GET['post_type'] ) ) ? sanitize_text_field( $_GET['post_type'] ) : 0;  // WPCS: CSRF ok.
			switch ( $post_type ) {
				case 'post_type_actors':
					LWTV_Actors_Calculate::do_the_math( $post_id );
					break;
			}
		}
	}

	/*
	 * CPT Settings
	 *
	 */
	public function create_post_type() {
		$labels = array(
			'name'                  => 'Actors',
			'singular_name'         => 'Actor',
			'menu_name'             => 'Actors',
			'parent_item_colon'     => 'Parent Actor:',
			'all_items'             => 'All Actors',
			'view_item'             => 'View Actor',
			'add_new_item'          => 'Add New Actor',
			'add_new'               => 'Add New',
			'edit_item'             => 'Edit Actor',
			'update_item'           => 'Update Actor',
			'search_items'          => 'Search Actors',
			'not_found'             => 'No actors found',
			'not_found_in_trash'    => 'No actors in the Trash',
			'featured_image'        => 'Actor Photo',
			'set_featured_image'    => 'Set Actor Photo',
			'remove_featured_image' => 'Remove Actor Photo',
			'use_featured_image'    => 'Use as Actor Photo',
		);
		$args   = array(
			'label'               => 'post_type_actors',
			'description'         => 'Actors',
			'labels'              => $labels,
			'public'              => true,
			'show_in_rest'        => true,
			'rest_base'           => 'actor',
			'menu_position'       => 7,
			'menu_icon'           => 'dashicons-id',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'has_archive'         => 'actors',
			'rewrite'             => array( 'slug' => 'actor' ),
			'delete_with_user'    => false,
			'exclude_from_search' => false,
		);
		register_post_type( 'post_type_actors', $args );
	}

	/*
	 * Custom Taxonomies
	 *
	 */
	public function create_taxonomies() {

		$taxonomies = array(
			'gender'    => 'actor_gender',
			'sexuality' => 'actor_sexuality',
		);

		foreach ( $taxonomies as $pretty => $slug ) {
			// Labels for taxonomy
			$labels = array(
				'name'                       => ucwords( $pretty ) . 's',
				'singular_name'              => ucwords( $pretty ),
				'search_items'               => 'Search ' . ucwords( $pretty ) . 's',
				'popular_items'              => 'Popular ' . ucwords( $pretty ) . 's',
				'all_items'                  => 'All' . ucwords( $pretty ) . 's',
				'edit_item'                  => 'Edit ' . ucwords( $pretty ),
				'update_item'                => 'Update ' . ucwords( $pretty ),
				'add_new_item'               => 'Add New ' . ucwords( $pretty ),
				'new_item_name'              => 'New' . ucwords( $pretty ) . 'Name',
				'separate_items_with_commas' => 'Separate ' . $pretty . 's with commas',
				'add_or_remove_items'        => 'Add or remove' . $pretty . 's',
				'choose_from_most_used'      => 'Choose from the most used ' . $pretty . 's',
				'not_found'                  => 'No ' . ucwords( $pretty ) . 's found.',
				'menu_name'                  => ucwords( $pretty ) . 's',
			);
			//parameters for the new taxonomy
			$arguments = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_in_rest'          => false,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'show_in_nav_menus'     => true,
				'rewrite'               => array( 'slug' => rtrim( $slug, 's' ) ),
			);
			// Taxonomy name
			$taxonomyname = 'lez_' . $slug;

			// Register taxonomy
			register_taxonomy( $taxonomyname, 'post_type_actors', $arguments );
		}
	}

	/*
	 * Create Custom Columns
	 * Used by quick edit, etc
	 */
	public function manage_posts_columns( $columns ) {
		$columns['actors-queer']     = '<span class="dashicons dashicons-smiley"><span class="screen-reader-text">Queer IRL</span></span>';
		$columns['actors-charcount'] = '<span class="dashicons dashicons-nametag"><span class="screen-reader-text">Characters</span></span>';
		return $columns;
	}

	/*
	 * Add Custom Column Content
	 */
	public function manage_posts_custom_column( $column, $post_id ) {
		switch ( $column ) {
			case 'actors-queer':
				$is_queer = get_post_meta( $post_id, 'lezactors_queer', true );
				$queer    = ( $is_queer ) ? 'Y' : 'N';
				echo esc_html( $queer );
				break;
			case 'actors-charcount':
				$charcount = get_post_meta( $post_id, 'lezactors_char_count', true );
				echo (int) $charcount;
				break;
		}
	}

	/*
	 * Make Custom Columns Sortable
	 */
	public function manage_edit_sortable_columns( $columns ) {
		$columns['actors-charcount'] = 'characters';  // Allow sort by queers
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
				case 'characters':
					$query->set( 'meta_key', 'lezactors_char_count' );
					$query->set( 'orderby', 'meta_value_num' );
					break;
			}
		}
	}

	/*
	 * Save post meta for actors
	 *
	 * @param int $post_id The post ID.
	 * @param post $post The post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 */
	public function update_meta( $post_id ) {

		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post_post_type_actors', array( $this, 'update_meta' ) );

		// Do the math
		LWTV_Actors_Calculate::do_the_math( $post_id );

		// re-hook this function
		add_action( 'save_post_post_type_actors', array( $this, 'update_meta' ) );
	}

	/*
	 * Save post meta for shows on CHARACTER update
	 *
	 * This will update the metakey 'lezactors_char_count' on save
	 *
	 * @param int $post_id The post ID.
	 */
	public function update_meta_from_chars( $post_id ) {
		$character_actor_ids = get_post_meta( $post_id, 'lezchars_actor', true );

		if ( '' !== $character_actor_ids ) {
			foreach ( $character_actor_ids as $actor ) {
				do_action( 'do_update_actor_meta', $actor );
			}
		}
	}

	/*
	 * Extra Meta Variables for Yoast and Characters
	 *
	 * Information on how many queer characters an actor plays
	 */
	public function yoast_retrieve_characters_replacement() {
		global $post;
		$char_count = get_post_meta( $post->ID, 'lezactors_char_count', true );
		// translators: %s is the number of characters
		$characters = ( 0 === $char_count ) ? 'no characters' : sprintf( _n( '%s character', '%s characters', $char_count ), $char_count );
		return $characters;
	}

	/*
	 * Extra Meta Variables for Yoast and Queer
	 *
	 * List of actors who played a character, for use on character pages
	 */
	public function yoast_retrieve_queer_replacement() {
		global $post;
		$is_queer = get_post_meta( $post->ID, 'lezactors_queer', true );
		$queer    = ( $is_queer ) ? 'a queer actor' : 'an actor';
		return $queer;
	}

	/*
	 * Extra Replacement Functions for Yoast SEO
	 */
	public function yoast_seo_register_extra_replacements() {
		wpseo_register_var_replacement( '%%characters%%', array( $this, 'yoast_retrieve_characters_replacement' ), 'basic', 'Information on how many characters an actor plays.' );
		wpseo_register_var_replacement( '%%is_queer%%', array( $this, 'yoast_retrieve_queer_replacement' ), 'basic', 'Output if the actor is queer IRL.' );
	}

	/*
	 * AMP
	 */
	public function amp_init() {
		add_post_type_support( 'post_type_actors', AMP_QUERY_VAR );
	}

	/*
	 * Add to 'Right Now'
	 */
	public function dashboard_glance_items() {
		foreach ( array( 'post_type_actors' ) as $post_type ) {
			$num_posts = wp_count_posts( $post_type );
			if ( $num_posts && $num_posts->publish ) {
				if ( 'post_type_actors' === $post_type ) {
					// translators: %s is the number of actors
					$text = _n( '%s Actor', '%s Actors', $num_posts->publish );
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
			#adminmenu #menu-posts-post_type_actors div.wp-menu-image:before, #dashboard_right_now li.post_type_actors-count a:before {
				content: '\\f336';
				margin-left: -1px;
			}

			.fixed th.column-actors-charcount, .fixed th.column-actors-queer {
				width: 4em;
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
			case 'post_type_actors':
				$countqueers = get_post_meta( $post->ID, 'lezactors_char_count', true );
				$deadqueers  = get_post_meta( $post->ID, 'lezactors_dead_count', true );
				echo '<div class="misc-pub-section lwtv misc-pub-lwtv">
					<span id="characters">Characters: <b>' . (int) $countqueers . '</b> total';
				if ( $deadqueers ) {
					echo '/ <b>' . (int) $deadqueers . '</b> dead';
				}
				echo '</span>
				</div>';
				break;
		}
	}

}

// Include Sub Files
require_once 'actors/calculations.php';
require_once 'actors/cmb2-metaboxes.php';


new LWTV_CPT_Actors();
