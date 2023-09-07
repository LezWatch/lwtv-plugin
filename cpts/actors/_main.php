<?php
/*
 * Custom Post Type for actors on LWTV
 *
 * @since 1.0
 */

// Include Sub Files
require_once 'calculations.php';
require_once 'cmb2-metaboxes.php';
require_once 'custom-columns.php';

/**
 * class LWTV_CPT_Actors
 */
class LWTV_CPT_Actors {

	protected static $all_taxonomies;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		// Create CPT and Taxes
		add_action( 'init', array( $this, 'create_post_type' ), 0 );
		add_action( 'init', array( $this, 'create_taxonomies' ), 0 );

		// Yoast Hooks
		add_action( 'wpseo_register_extra_replacements', array( $this, 'yoast_seo_register_extra' ) );

		// Save Hooks
		add_action( 'save_post_post_type_actors', array( $this, 'save_post_meta' ), 10, 3 );

		// Define show taxonomies
		// SLUG => PRETTY NAME
		self::$all_taxonomies = array(
			'lez_actor_gender'    => array( 'name' => 'gender' ),
			'lez_actor_sexuality' => array(
				'name'   => 'sexuality',
				'plural' => 'sexualities',
			),
			'lez_actor_romantic'  => array( 'name' => 'romantic orientation' ),
			'lez_actor_pronouns'  => array( 'name' => 'pronoun' ),
		);

		// phpcs:disable
		// Hide taxonomies from Gutenberg.
		// While this isn't the official API for this need, it works.
		// https://github.com/WordPress/gutenberg/issues/6912#issuecomment-428403380
		add_filter( 'rest_prepare_taxonomy', function( $response, $taxonomy ) {

			$all_tax_array = array();
			foreach ( self::$all_taxonomies as $actor_tax => $actor_array ) {
				if ( ! isset( $actor_array['hide'] ) || false !== $actor_array['hide'] ) {
					$all_tax_array[] = $actor_tax;
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
	 * Admin Init
	 */
	public function admin_init() {
		add_action( 'admin_head', array( $this, 'admin_css' ) );
		add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
		add_filter( 'enter_title_here', array( $this, 'custom_enter_title' ) );
	}

	/*
	 * CPT Settings
	 *
	 */
	public function create_post_type() {

		$actor_taxonomies = array();
		foreach ( self::$all_taxonomies as $actor_tax => $actor_array ) {
			$actor_taxonomies[] = $actor_tax;
		}

		$labels   = array(
			'name'                     => 'Actors',
			'singular_name'            => 'Actor',
			'menu_name'                => 'Actors',
			'add_new_item'             => 'Add New Actor',
			'edit_item'                => 'Edit Actor',
			'new_item'                 => 'New Actor',
			'view_item'                => 'View Actor',
			'all_items'                => 'All Actors',
			'search_items'             => 'Search Actors',
			'not_found'                => 'No Actors found',
			'not_found_in_trash'       => 'No Actors found in Trash',
			'update_item'              => 'Update Actor',
			'featured_image'           => 'Actor Image',
			'set_featured_image'       => 'Set Actor Image (recommended 350 x 412)',
			'remove_featured_image'    => 'Remove Actor Image',
			'use_featured_image'       => 'Use as Actor Image',
			'archives'                 => 'Actor archives',
			'insert_into_item'         => 'Insert into Actor',
			'uploaded_to_this_item'    => 'Uploaded to this Actor',
			'filter_items_list'        => 'Filter Actor list',
			'items_list_navigation'    => 'Actor list navigation',
			'items_list'               => 'Actor list',
			'item_published'           => 'Actor published.',
			'item_published_privately' => 'Actor published privately.',
			'item_reverted_to_draft'   => 'Actor reverted to draft.',
			'item_scheduled'           => 'Actor scheduled.',
			'item_updated'             => 'Actor updated.',
		);
		$template = array(
			array( 'lez-library/featured-image' ),
			array(
				'core/paragraph',
				array( 'placeholder' => 'Everything we need to know about this actor.' ),
			),
		);
		$args     = array(
			'label'               => 'post_type_actors',
			'description'         => 'Actors',
			'labels'              => $labels,
			'public'              => true,
			'show_in_rest'        => true,
			'template'            => $template,
			'rest_base'           => 'actor',
			'menu_position'       => 7,
			'menu_icon'           => 'dashicons-id',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			'has_archive'         => 'actors',
			'rewrite'             => array( 'slug' => 'actor' ),
			'taxonomies'          => $actor_taxonomies,
			'delete_with_user'    => false,
			'exclude_from_search' => false,
			'capability_type'     => array( 'actor', 'actors' ),
			'map_meta_cap'        => true,
		);
		register_post_type( 'post_type_actors', $args );
	}

	/*
	 * Custom Taxonomies
	 *
	 */
	public function create_taxonomies() {
		foreach ( self::$all_taxonomies as $tax_slug => $tax_array ) {
			// Remove lez_ from slug.
			$slug = str_replace( 'lez_', '', $tax_slug );

			// Determine names.
			$name_singular = ucwords( $tax_array['name'] );
			$name_plural   = ( isset( $tax_array['plural'] ) ) ? ucwords( $tax_array['plural'] ) : ucwords( $tax_array['name'] ) . 's';

			// Labels for taxonomy
			$labels = array(
				'name'                       => $name_plural,
				'singular_name'              => $name_singular,
				'search_items'               => 'Search ' . $name_plural,
				'popular_items'              => 'Popular ' . $name_plural,
				'all_items'                  => 'All' . $name_plural,
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
				'rewrite'               => array( 'slug' => rtrim( $slug, 's' ) ),
			);

			// Register taxonomy
			register_taxonomy( $tax_slug, 'post_type_actors', $arguments );
		}
	}

	/*
	 * Save post meta for actors
	 *
	 * @param int $post_id The post ID.
	 * @param post $post The post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 */
	public function save_post_meta( $post_id ) {

		// Prevent running on autosave.
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return;
		}

		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post_post_type_actors', array( $this, 'save_post_meta' ) );

		// Do the math
		( new LWTV_Actors_Calculate() )->do_the_math( $post_id );

		// re-hook this function
		add_action( 'save_post_post_type_actors', array( $this, 'save_post_meta' ) );
	}

	/*
	 * Extra Meta Variables for Yoast and Characters
	 *
	 * Information on how many queer characters an actor plays
	 */
	public function yoast_retrieve_characters_replacement() {
		global $post;

		$characters = '0';
		if ( is_object( $post ) ) {
			$char_count = get_post_meta( $post->ID, 'lezactors_char_count', true );
			// translators: %s is the number of characters
			$characters = ( 0 === $char_count ) ? 'no characters' : sprintf( _n( '%s character', '%s characters', $char_count ), $char_count );
		}

		return $characters;
	}

	/*
	 * Extra Meta Variables for Yoast and Queer
	 *
	 * List of actors who played a character, for use on character pages
	 */
	public function yoast_retrieve_queer_replacement() {
		global $post;

		$queer = 'an actor';
		if ( is_object( $post ) ) {
			$is_queer = get_post_meta( $post->ID, 'lezactors_queer', true );
			$queer    = ( $is_queer ) ? 'a queer actor' : 'an actor';
		}
		return $queer;
	}

	/*
	 * Extra Replacement Functions for Yoast SEO
	 */
	public function yoast_seo_register_extra() {
		wpseo_register_var_replacement( '%%characters%%', array( $this, 'yoast_retrieve_characters_replacement' ), 'basic', 'Information on how many characters an actor plays.' );
		wpseo_register_var_replacement( '%%is_queer%%', array( $this, 'yoast_retrieve_queer_replacement' ), 'basic', 'Output if the actor is queer IRL.' );
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

		if ( is_object( $post ) ) {
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

	/*
	 * Customize title
	 */
	public function custom_enter_title( $input ) {
		if ( 'post_type_actors' === get_post_type() ) {
			$input = 'Add actor';
		}
		return $input;
	}
}

new LWTV_CPT_Actors();
