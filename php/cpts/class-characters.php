<?php
/*
 * Custom Post Type for characters on LWTV
 *
 * @since 1.0
 */

namespace LWTV\CPTs;

use LWTV\CPTs\Characters\Calculations;
use LWTV\CPTs\Characters\CMB2_Metaboxes;
use LWTV\CPTs\Characters\Custom_Columns;

/**
 * class LWTV_CPT_Characters
 */
class Characters {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const SLUG = 'post_type_characters';

	/**
	 * All Taxonomies
	 *
	 * @var array
	 */
	const ALL_TAXONOMIES = array(
		'lez_cliches'   => array( 'name' => 'cliché' ),
		'lez_gender'    => array( 'name' => 'gender' ),
		'lez_sexuality' => array( 'name' => 'sexual orientation' ),
		'lez_romantic'  => array( 'name' => 'romantic orientation' ),
	);

	/**
	 * Shadow Taxonomy
	 */
	const SHADOW_TAXONOMY = 'shadow_tax_characters';

	/**
	 * Constructor
	 */
	public function __construct() {
		new CMB2_Metaboxes();
		new Custom_Columns();

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		// Cron action to update meta when saved.
		add_action( 'lwtv_update_char_meta', array( $this, 'update_char_meta' ) );

		// Create CPT and Taxes
		add_action( 'init', array( $this, 'create_post_type' ), 0 );
		add_action( 'init', array( $this, 'create_taxonomies' ), 0 );
		add_action( 'init', array( $this, 'create_shadow_taxonomies' ), 0 );

		// Yoast Hooks
		add_action( 'wpseo_register_extra_replacements', array( $this, 'yoast_seo_register_extra_replacements' ) );

		// phpcs:disable
		// Hide taxonomies from Gutenberg.
		// While this isn't the official API for this need, it works.
		// https://github.com/WordPress/gutenberg/issues/6912#issuecomment-428403380
		add_filter( 'rest_prepare_taxonomy', function( $response, $taxonomy ) {

			$all_tax_array = array();
			foreach ( self::ALL_TAXONOMIES as $char_tax => $char_array ) {
				if ( ! isset( $char_tax['hide'] ) || false !== $char_array['hide'] ) {
					$all_tax_array[] = $char_tax;
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
		add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
		add_action( 'save_post_post_type_characters', array( $this, 'save_post_meta' ), 10, 3 );
		add_filter( 'enter_title_here', array( $this, 'custom_enter_title' ) );
	}

	/*
	 * Create Post Type
	 *
	 * post_type_characters
	 */
	public function create_post_type() {

		$char_taxonomies = array();
		foreach ( self::ALL_TAXONOMIES as $slug => $more ) {
			$char_taxonomies[] = $slug;
		}

		$labels   = array(
			'name'                     => 'Characters',
			'singular_name'            => 'Character',
			'menu_name'                => 'Characters',
			'add_new_item'             => 'Add New Character',
			'edit_item'                => 'Edit Character',
			'new_item'                 => 'New Character',
			'view_item'                => 'View Character',
			'all_items'                => 'All Characters',
			'search_items'             => 'Search Characters',
			'not_found'                => 'No Characters found',
			'not_found_in_trash'       => 'No Characters found in Trash',
			'update_item'              => 'Update Character',
			'featured_image'           => 'Character Image',
			'set_featured_image'       => 'Set Character Image (recommended 350 x 412)',
			'remove_featured_image'    => 'Remove Character Image',
			'use_featured_image'       => 'Use as Character Image',
			'archives'                 => 'Character archives',
			'insert_into_item'         => 'Insert into Character',
			'uploaded_to_this_item'    => 'Uploaded to this Character',
			'filter_items_list'        => 'Filter Character list',
			'items_list_navigation'    => 'Character list navigation',
			'items_list'               => 'Character list',
			'item_published'           => 'Character published.',
			'item_published_privately' => 'Character published privately.',
			'item_reverted_to_draft'   => 'Character reverted to draft.',
			'item_scheduled'           => 'Character scheduled.',
			'item_updated'             => 'Character updated.',
		);
		$template = array(
			array( 'lez-library/featured-image' ),
			array(
				'core/paragraph',
				array( 'placeholder' => 'Everything we need to know about this character' ),
			),
		);
		$args     = array(
			'label'               => self::SLUG,
			'description'         => 'Characters',
			'labels'              => $labels,
			'public'              => true,
			'show_in_rest'        => true,
			'template'            => $template,
			'rest_base'           => 'character',
			'menu_position'       => 7,
			'menu_icon'           => 'dashicons-nametag',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
			'has_archive'         => 'characters',
			'rewrite'             => array( 'slug' => 'character' ),
			'taxonomies'          => $char_taxonomies,
			'delete_with_user'    => false,
			'exclude_from_search' => false,
			'capability_type'     => array( 'character', 'characters' ),
			'map_meta_cap'        => true,
		);
		register_post_type( self::SLUG, $args );
	}

	/*
	 * Custom Taxonomies
	 */
	public function create_taxonomies() {

		foreach ( self::ALL_TAXONOMIES as $tax_slug => $tax_array ) {
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
				'public'                => true,
				'show_ui'               => true,
				'show_in_rest'          => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'show_in_nav_menus'     => true,
				'rewrite'               => array( 'slug' => rtrim( $slug, 's' ) ),
				'rest_base'             => rtrim( $slug, 's' ),
			);

			// Register taxonomy
			register_taxonomy( $tax_slug, self::SLUG, $arguments );
		}
	}

	/**
	 * Registers shadow taxonomy for being able to relate Characters to TV shows and actors.
	 * Think of it as we're adding the taxonomy for the character to the show and actor CPT.
	 *
	 * See https://packagist.org/packages/spock/shadow-taxonomies for more information.
	 */
	public function create_shadow_taxonomies() {
		$show_ui = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? true : false;

		register_taxonomy(
			self::SHADOW_TAXONOMY,
			array( Actors::SLUG, Shows::SLUG ),
			array(
				'label'         => 'SHADOW Characters',
				'rewrite'       => false,
				'show_tagcloud' => false,
				'show_ui'       => $show_ui,
				'public'        => false,
				'hierarchical'  => false,
				'show_in_menu'  => $show_ui,
				'meta_box_cb'   => false,
			)
		);

		\Shadow_Taxonomy\Core\create_relationship( self::SLUG, self::SHADOW_TAXONOMY );
	}

	/*
	 * Add to 'Right Now'
	 */
	public function dashboard_glance_items() {
		foreach ( array( self::SLUG ) as $post_type ) {
			$num_posts = wp_count_posts( $post_type );
			if ( $num_posts && $num_posts->publish ) {
				if ( self::SLUG === $post_type ) {
					// translators: %s is the number of characters
					$text = _n( '%s Character', '%s Characters', $num_posts->publish );
				}
				$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );
				printf( '<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', esc_attr( $post_type ), esc_html( $text ) );
			}
		}
	}

	/*
	 * Customize title
	 */
	public function custom_enter_title( $input ) {
		if ( self::SLUG === get_post_type() ) {
			$input = 'Add character';
		}
		return $input;
	}

	/*
	 * Extra Meta Variables for Yoast and Actors
	 *
	 * List of actors who played a character, for use on character pages
	 */
	public function yoast_retrieve_actors_replacement() {
		global $post;

		$return = 'Unknown';
		if ( is_object( $post ) ) {
			$actors     = array();
			$actors_ids = get_post_meta( $post->ID, 'lezchars_actor', true );
			if ( ! is_array( $actors_ids ) ) {
				$actors_ids = array( get_post_meta( $post->ID, 'lezchars_actor', true ) );
			}
			if ( '' !== $actors_ids && ! is_null( $actors_ids ) ) {
				foreach ( $actors_ids as $each_actor ) {
					array_push( $actors, get_the_title( $each_actor ) );
				}
			}
			$return = implode( ', ', $actors );
		}

		return $return;
	}

	/*
	 * Extra Meta Variables for Yoast and Characters
	 *
	 * List of shows featuring a character, for use on character pages
	 */
	public function yoast_retrieve_shows_replacement() {
		global $post;

		$shows_string = '';
		if ( is_object( $post ) ) {
			$shows_ids    = get_post_meta( $post->ID, 'lezchars_show_group', true );
			$shows_titles = array();

			if ( ! is_array( $shows_ids ) ) {
				$shows_ids = array( $shows_ids );
			}

			if ( '' !== $shows_ids && ! is_null( $shows_ids ) ) {
				foreach ( $shows_ids as $each_show ) {

					// De-Array.
					if ( is_array( $each_show['show'] ) ) {
						$each_show['show'] = $each_show['show'][0];
					}

					// Get titles.
					if ( isset( $each_show['show'] ) ) {
						array_push( $shows_titles, get_the_title( $each_show['show'] ) );
					}
				}
			}
			$shows_string = implode( ', ', $shows_titles );
		}
		return $shows_string;
	}

	/*
	 * Extra Replacement Functions for Yoast SEO
	 */
	public function yoast_seo_register_extra_replacements() {
		\wpseo_register_var_replacement( '%%actors%%', array( $this, 'yoast_retrieve_actors_replacement' ), 'basic', 'A list of actors who played the character, separated by commas.' );
		\wpseo_register_var_replacement( '%%shows%%', array( $this, 'yoast_retrieve_shows_replacement' ), 'basic', 'A list of shows the character was on, separated by commas.' );
	}

	/*
	 * Save post meta for characters
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_post_meta( $post_id ) {

		// Prevent running on autosave.
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return;
		}

		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post_post_type_characters', array( $this, 'save_post_meta' ) );

		$this->schedule_cron( $post_id );

		// re-hook this function
		add_action( 'save_post_post_type_characters', array( $this, 'save_post_meta' ) );
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

	/*
	 * Schedule the cron job
	 *
	 * @param int $post_id The post ID.
	 */
	public function schedule_cron( $post_id ) {
		// Schedule the cron job.
		if ( ! wp_next_scheduled( 'lwtv_save_char_meta' ) ) {
			wp_schedule_single_event( time(), 'lwtv_update_char_meta', array( $post_id ) );
		}
	}

	/*
	 * Update post meta for characters
	 *
	 * @param int $post_id The post ID.
	 */
	public function update_char_meta( $post_id ) {
		// Fix Shows - you only get one!
		$this->fix_shows( $post_id );

		// Character scores and sync taxonomies
		$this->do_the_math( $post_id );

		// Always Sync Taxonomies
		lwtv_plugin()->save_select2_taxonomy( $post_id, 'lezchars_cliches', 'lez_cliches' );

		// Get a list of URLs to flush
		$clear_urls = lwtv_plugin()->collect_cache_urls_for_characters( $post_id );

		// If we've got a list of URLs, then flush.
		if ( isset( $clear_urls ) && ! empty( $clear_urls ) ) {
			lwtv_plugin()->clean_cache_urls( $clear_urls );
		}
	}

	/**
	 * Fix shows.
	 *
	 * At some point the show-group made an array for each show in a group, which is just
	 * wrong. This makes sure to DE-array them.
	 *
	 * @param  int  $post_id
	 * @return void
	 */
	public function fix_shows( $post_id ) {
		$all_shows = get_post_meta( $post_id, 'lezchars_show_group', true );
		$new_shows = array();

		if ( is_array( $all_shows ) ) {
			foreach ( $all_shows as $each_show ) {
				// If it's an array, de-array it.
				if ( is_array( $each_show['show'] ) ) {
					$each_show['show'] = reset( $each_show['show'] );
				}
				$new_shows[] = $each_show;
			}
			update_post_meta( $post_id, 'lezchars_show_group', $new_shows );
		}
	}
}
