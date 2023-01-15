<?php
/*
 * Custom Post Type for characters on LWTV
 *
 * @since 1.0
 */

// Include Sub Files
require_once 'calculations.php';
require_once 'cmb2-metaboxes.php';
require_once 'custom-columns.php';

/**
 * class LWTV_CPT_Characters
 */
class LWTV_CPT_Characters {

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
		add_action( 'wpseo_register_extra_replacements', array( $this, 'yoast_seo_register_extra_replacements' ) );

		// Define show taxonomies
		self::$all_taxonomies = array(
			'lez_cliches'   => array( 'name' => 'cliché' ),
			'lez_gender'    => array( 'name' => 'gender' ),
			'lez_sexuality' => array( 'name' => 'sexual orientation' ),
			'lez_romantic'  => array( 'name' => 'romantic orientation' ),
		);

		// phpcs:disable
		// Hide taxonomies from Gutenberg.
		// While this isn't the official API for this need, it works.
		// https://github.com/WordPress/gutenberg/issues/6912#issuecomment-428403380
		add_filter( 'rest_prepare_taxonomy', function( $response, $taxonomy ) {

			$all_tax_array = array();
			foreach ( self::$all_taxonomies as $char_tax => $char_array ) {
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
		add_action( 'admin_head', array( $this, 'admin_css' ) );
		add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
		add_action( 'save_post_post_type_characters', array( $this, 'save_post_meta' ), 10, 3 );
		add_filter( 'enter_title_here', array( $this, 'custom_enter_title' ) );
	}

	/*
	 * CPT Settings
	 */
	public function create_post_type() {

		$char_taxonomies = array();
		foreach ( self::$all_taxonomies as $slug => $array ) {
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
			'set_featured_image'       => 'Set Character Image (Min. 350 x 412)',
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
			'label'               => 'post_type_characters',
			'description'         => 'Characters',
			'labels'              => $labels,
			'public'              => true,
			'show_in_rest'        => true,
			'template'            => $template,
			'rest_base'           => 'character',
			'menu_position'       => 7,
			'menu_icon'           => 'dashicons-nametag',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			'has_archive'         => 'characters',
			'rewrite'             => array( 'slug' => 'character' ),
			'taxonomies'          => $char_taxonomies,
			'delete_with_user'    => false,
			'exclude_from_search' => false,
			'capability_type'     => array( 'character', 'characters' ),
			'map_meta_cap'        => true,
		);
		register_post_type( 'post_type_characters', $args );
	}

	/*
	 * Custom Taxonomies
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
				'rewrite'               => array( 'slug' => rtrim( $slug, 's' ) ),
				'rest_base'             => rtrim( $slug, 's' ),
			);

			// Register taxonomy
			register_taxonomy( $tax_slug, 'post_type_characters', $arguments );
		}
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
		wpseo_register_var_replacement( '%%actors%%', array( $this, 'yoast_retrieve_actors_replacement' ), 'basic', 'A list of actors who played the character, separated by commas.' );
		wpseo_register_var_replacement( '%%shows%%', array( $this, 'yoast_retrieve_shows_replacement' ), 'basic', 'A list of shows the character was on, separated by commas.' );
	}

	/*
	 * Add to 'Right Now'
	 */
	public function dashboard_glance_items() {
		foreach ( array( 'post_type_characters' ) as $post_type ) {
			$num_posts = wp_count_posts( $post_type );
			if ( $num_posts && $num_posts->publish ) {
				if ( 'post_type_characters' === $post_type ) {
					// translators: %s is the number of characters
					$text = _n( '%s Character', '%s Characters', $num_posts->publish );
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
			#adminmenu #menu-posts-post_type_characters div.wp-menu-image:before, #dashboard_right_now li.post_type_characters-count a:before {
				content: '\\f484';
				margin-left: -1px;
			}
		</style>";
	}

	/**
	 * list_characters function.
	 *
	 * @access public
	 * @static
	 * @param mixed $show_id
	 * @param string $output (default: 'query')
	 * @return void
	 */
	public function list_characters( $show_id, $output = 'query' ) {

		// Get array of characters (by ID)
		$characters = get_post_meta( $show_id, 'lezshows_char_list', true );

		// If the character list is empty, we must build it
		if ( empty( $characters ) ) {
			// Loop to get the list of characters
			$charactersloop = ( new LWTV_Loops() )->post_meta_query( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );

			if ( $charactersloop->have_posts() ) {
				$characters = wp_list_pluck( $charactersloop->posts, 'ID' );
			}

			wp_reset_query();

			if ( is_array( $characters ) ) {
				$characters = array_unique( $characters );
			} else {
				$characters = array( $characters );
			}

			update_post_meta( $show_id, 'lezshows_char_list', $characters );
		}

		$char_counts = array(
			'total' => count( $characters ),
			'dead'  => 0,
			'none'  => 0,
			'quirl' => 0,
			'trans' => 0,
			'txirl' => 0,
		);

		if ( ! empty( $characters ) ) {
			foreach ( $characters as $char_id ) {
				// Get the list of shows.
				$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true );

				// If the character is in this show, AND a published character
				// we will pass the following data to the character template
				// to determine what to display
				if (
					'' !== $shows_array &&
					! empty( $shows_array ) &&
					'publish' === get_post_status( $char_id )
				) {
					foreach ( $shows_array as $char_show ) {
						if ( (int) $char_show['show'] === $show_id ) {
							// Get a list of actors (we need this twice later)
							$actors_ids = get_post_meta( $char_id, 'lezchars_actor', true );
							if ( ! is_array( $actors_ids ) ) {
								$actors_ids = array( $actors_ids );
							}

							// Increase the count of characters
							$char_counts['total']++;

							// Dead?
							if ( has_term( 'dead', 'lez_cliches', $char_id ) ) {
								$char_counts['dead']++;
							}
							// No cliches?
							if ( has_term( 'none', 'lez_cliches', $char_id ) ) {
								$char_counts['none']++;
							}
							// The Tambour Takedown: Checking Queer IRL
							// We don't award shows that have cast a cis/het actor in a queer
							// role. To solve this, we grab the actor listed as PRIMARY ACTOR
							// (i.e. the one listed first). If THEY are QIRL, the show gets points.
							if ( has_term( 'queer-irl', 'lez_cliches', $char_id ) ) {
								$top_actor = reset( $actors_ids );
								if ( 'yes' === ( new LWTV_Loops() )->is_actor_queer( $top_actor ) ) {
									$char_counts['quirl']++;
								}
							}

							// Is the character is not Cisgender ...
							$valid_trans_char = array( 'cisgender', 'intersex', 'unknown' );
							if ( ! has_term( $valid_trans_char, 'lez_gender', $char_id ) ) {
								$char_counts['trans']++;
							}

							// If an actor is transgender, we get an extra bonus.
							foreach ( $actors_ids as $actor ) {
								if ( 'yes' === ( new LWTV_Loops() )->is_actor_trans( $actor ) ) {
									$char_counts['txirl']++;
								}
							}
						}
					}
				}
			}
		}

		switch ( $output ) {
			case 'dead':
				// Count of dead characters
				$return = $char_counts['dead'];
				break;
			case 'none':
				// count of characters with NO clichés
				$return = $char_counts['none'];
				break;
			case 'queer-irl':
				// count of characters who are queer IRL
				$return = $char_counts['quirl'];
				break;
			case 'trans':
				// Count of trans characters
				$return = $char_counts['trans'];
				break;
			case 'trans-irl':
				// count of characters who are trans IRL
				$return = $char_counts['txirl'];
				break;
			case 'query':
				// WP Array of all characters
				$return = $characters;
				break;
			case 'count':
				// Count of all characters on the show
				$return = $char_counts['total'];
				break;
		}

		return $return;
	}

	/**
	 * Get Characters For Show
	 *
	 * Get all the characters for a show, based on role type.
	 *
	 * @access public
	 * @param mixed $show_id: Extracted from page the function is called on
	 * @param mixed $role: regular (default), recurring, guest
	 * @return array of characters
	 */
	public function get_chars_for_show( $show_id, $havecharcount, $role = 'regular' ) {

		/**
		 * Funny things:
		 *   - The Sara Lance Complexity -- Because someone is on a lot of shows,
		 *                                  we have to make sure the IDs are right
		 *                                  and the show isn't a partial match.
		 *                                  Sara hasn't been on EVERY show yet.
		 *   - The Shane Clause          -- Thanks to Shane sleeping with everyone,
		 *                                  we had to limit this loop to 100 minimum
		 *   - The Clone Club Corollary  -- Sarah Manning took the place of every
		 *                                  single other character played by Tatiana
		 *                                  Maslany.
		 *   - The Vanishing Xenaphobia  -- When set to under 200, Xena doesn't show
		 *                                  on the Xena:WP show page
		 *   - Just a Phase Samantha     -- By the time we hit 6000 characters, the math
		 *                                  stopped working to show all the characters.
		 *                                  Now it's set to 1/10th the number of chars.
		 *
		 * Calculate the max number of characters to list, based on the
		 * previous count. Default/Minimum is the number of characters divided by 10
		 */

		// Valid Roles:
		$valid_roles = array( 'regular', 'recurring', 'guest' );

		// If this isn't a show page, or there are no valid roles, bail.
		if ( ! isset( $show_id ) || 'post_type_shows' !== get_post_type( $show_id ) || ! in_array( $role, $valid_roles, true ) ) {
			return;
		}

		// Get array of characters (by ID)
		$characters = get_post_meta( $show_id, 'lezshows_char_list', true );

		// If the character list is empty, we must build it
		if ( empty( $characters ) ) {
			// Loop to get the list of characters
			$charactersloop = ( new LWTV_Loops() )->post_meta_query( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );

			if ( $charactersloop->have_posts() ) {
				$characters = wp_list_pluck( $charactersloop->posts, 'ID' );
			}

			$characters = array_unique( $characters );
			update_post_meta( $post_id, 'lezshows_char_list', $characters );

			// Reset to end
			wp_reset_query();
		}

		// Empty array to display later
		$display    = array();
		$characters = array_unique( $characters );

		foreach ( $characters as $char_id ) {
			$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true );

			// If the character is in this show, AND a published character,
			// AND has this role ON THIS SHOW we will pass the following
			// data to the character template to determine what to display.
			if ( 'publish' === get_post_status( $char_id ) && isset( $shows_array ) && ! empty( $shows_array ) ) {
				foreach ( $shows_array as $char_show ) {
					// Because of show IDs having SIMILAR numbers, we need to be a little more flex
					// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					if ( $char_show['show'] == $show_id && $char_show['type'] === $role ) {
						$display[ $char_id ] = array(
							'id'        => $char_id,
							'title'     => get_the_title( $char_id ),
							'url'       => get_the_permalink( $char_id ),
							'content'   => get_the_content( $char_id ),
							'shows'     => $shows_array,
							'show_from' => $show_id,
							'role_from' => $role,
						);
					}
				}
			}
		}

		return $display;
	}

	/*
	 * Save post meta for characters
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
		remove_action( 'save_post_post_type_characters', array( $this, 'save_post_meta' ) );

		// Character scores
		( new LWTV_Characters_Calculate() )->do_the_math( $post_id );

		// Get a list of URLs to flush
		$clear_urls = ( new LWTV_Cache() )->collect_urls_for_characters( $post_id );

		// Always Sync Taxonomies
		( new LWTV_CMB2_Addons() )->select2_taxonomy_save( $post_id, 'lezchars_cliches', 'lez_cliches' );

		// Always update Wikidata
		( new LWTV_Debug() )->check_actors_wikidata( $post_id );

		// If we've got a list of URLs, then flush.
		if ( isset( $clear_urls ) && ! empty( $clear_urls ) ) {
			( new LWTV_Cache() )->clean_urls( $clear_urls );
		}

		// re-hook this function
		add_action( 'save_post_post_type_characters', array( $this, 'save_post_meta' ) );
	}

	/*
	 * Customize title
	 */
	public function custom_enter_title( $input ) {
		if ( 'post_type_characters' === get_post_type() ) {
			$input = 'Add character';
		}
		return $input;
	}
}

new LWTV_CPT_Characters();
