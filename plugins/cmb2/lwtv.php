<?php
/*
Description: Customizations for CMB2
Version: 2.0.2
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_CMB2
 *
 * Customize CMB2
 *
 * @since 1.0
 */
class LWTV_CMB2 {

	public $icon_taxonomies; // Taxonomies that have an icon
	public $symbolicon_path; // Path to symbolicons
	public $version; // Plugin version

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->version = '2.0.2';

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'cmb2_admin_init', array( $this, 'favorite_shows_user_profile_metabox' ) );

		$this->icon_taxonomies = array( 'lez_cliches', 'lez_tropes', 'lez_formats', 'lez_genres', 'lez_intersections' );

		// If we don't have symbolicons, there's not a reason to register the taxonomy box...
		if ( defined( 'LWTV_SYMBOLICONS_PATH' ) ) {
			$this->symbolicon_path = LWTV_SYMBOLICONS_PATH;
			add_action( 'cmb2_admin_init', array( $this, 'register_taxonomy_metabox' ) );
		}

		// Add all filters and actions to show icons on tax list page
		foreach ( $this->icon_taxonomies as $tax_name ) {
			add_filter( 'manage_edit-' . $tax_name . '_columns', array( $this, 'terms_column_header' ) );
			add_action( 'manage_' . $tax_name . '_custom_column', array( $this, 'terms_column_content' ), 10, 3 );
		}
	}

	/**
	 * Init
	 */
	public function admin_init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );
	}

	/**
	 * Extra Get post options.
	 */
	public function get_post_options( $post_type = 'post', $numberposts = 500 ) {

		// Build arguments, based on data sent.
		$args = array(
			'post_type'     => $post_type,
			'numberposts'   => (int) $numberposts + 50,
			'post_status'   => array( 'publish', 'pending', 'draft', 'future', 'private' ),
			'fields'        => 'ids',
			'no_found_rows' => true,
		);

		// Get the posts
		$posts = get_posts( $args );

		// Sort the posts.
		$post_options = array();
		if ( $posts ) {
			foreach ( $posts as $post ) {
				$post_title = get_the_title( $post );
				// If we're an actor, we should check for queerness.
				if ( 'post_type_actors' === $post_type ) {
					if ( get_post_meta( $post, 'lezactors_queer', true ) ) {
						$post_title .= ' (QUEER IRL)';
					}
				}

				// Add extra based on status.
				switch ( get_post_status( $post ) ) {
					case 'draft':
						$post_title .= ' - DRAFT';
						break;
					case 'future':
					case 'pending':
						$post_title .= ' - SCHEDULED';
						break;
					case 'private':
						$post_title .= ' - PRIVATE';
						break;
				}

				$post_options[ $post ] = $post_title;
			}
		}

		// Sort.
		if ( is_array( $post_options ) ) {
			asort( $post_options );
		}

		return $post_options;
	}

	/**
	 * Get default data for some odd CMB2 things using select2
	 * @param  string  $postmeta the name of the postmeta used by CMB2
	 * @param  string  $taxonomy the name of the taxonomy we're using
	 * @param  integer $post_id  post ID
	 * @param  boolean $none     does it have a 'none'?
	 * @return array             An array of Term IDs
	 */
	public function get_select2_defaults( $postmeta, $taxonomy, $post_id = 0, $none = false ) {

		if ( 0 === $post_id ) {
			return;
		}

		$get_postmeta    = get_post_meta( $post_id, $postmeta, true );
		$postmeta_to_add = array();
		if ( ! array( $get_postmeta ) || empty( $get_postmeta ) ) {
			// If NONE is true, we have some extra
			if ( $none ) {
				$get_taxonomy = wp_get_post_terms( $post_id, $taxonomy );
				if ( ! empty( $get_taxonomy ) && ! is_wp_error( $get_taxonomy ) ) {
					foreach ( $get_taxonomy as $the_term ) {
						$postmeta_to_add[] = $the_term->term_id;
					}
					update_post_meta( $post_id, $postmeta, $postmeta_to_add );
				}
			}
		}
		return $postmeta_to_add;
	}

	/**
	 * CSS tweaks
	 *
	 * @access public
	 * @param mixed $hook string - The filename of the page.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		wp_register_style( 'cmb-styles', plugins_url( 'cmb2.css', __FILE__ ), array(), $this->version );
		$post_array = array( 'edit-tags.php', 'post.php', 'post-new.php', 'term.php', 'page-new.php', 'page.php' );
		if ( in_array( $hook, $post_array, true ) ) {
			wp_enqueue_style( 'cmb-styles' );
		}
	}

	/**
	 * Add metabox to custom taxonomies to show icon
	 *
	 * $this->icon_taxonomies       array of taxonomies to show icons on.
	 * $this->symbolicon_path       location of Symbolicons
	 *
	 * register_taxonomy_metabox()  CMB2 mextabox code
	 * before_field_icon()          Show an icon if that exists
	 *
	 * @param  array                $field_args  Array of field parameters
	 * @param  CMB2_Field object    $field    Field object
	 * @return string - post content
	 */
	public function register_taxonomy_metabox() {
		$prefix         = 'lez_termsmeta_';
		$imagepath      = LWTV_SYMBOLICONS_PATH;
		$icon_array     = array();
		$symbolicon_url = admin_url( 'themes.php?page=symbolicons' );

		foreach ( glob( $imagepath . '*' ) as $filename ) {
			$filename                = str_replace( '.svg', '', str_replace( LWTV_SYMBOLICONS_PATH, '', $filename ) );
			$icon_array[ $filename ] = $filename;
		}

		$cmb_term = new_cmb2_box(
			array(
				'id'               => $prefix . 'edit',
				'title'            => 'Category Metabox',
				'object_types'     => array( 'term' ),
				'taxonomies'       => $this->icon_taxonomies,
				'new_term_section' => true,
			)
		);

		$cmb_term->add_field(
			array(
				'name'             => 'Icon',
				'desc'             => 'Select the icon you want to use. Once saved, it will show on the left.<br />If you need help visualizing, check out the <a href=' . $symbolicon_url . '>Symbolicons List</a>.',
				'id'               => $prefix . 'icon',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'custom',
				'options'          => $icon_array,
				'before_field'     => array( $this, 'before_field_icon' ),
			)
		);
	}

	// Add before field icon display
	public function before_field_icon( $field_args, $field ) {
		$icon = $field->value;

		// Bail early if empty
		if ( empty( $icon ) || ! defined( 'LWTV_SYMBOLICONS_PATH' ) ) {
			return;
		}

		if ( ! file_exists( LWTV_SYMBOLICONS_PATH . $icon . '.svg' ) ) {
			$content = 'N/A';
		} else {
			$filename = LWTV_SYMBOLICONS_URL . $icon . '.svg';
			$content  = '<span class="cmb2-icon" role="img">' . file_get_contents( $filename ) . '</span>';
		}

		return $content;
	}

	// Tax list column header
	public function terms_column_header( $columns ) {
		$columns['icon'] = 'Icon';
		return $columns;
	}

	// Tax list column content
	public function terms_column_content( $value, $content, $term_id ) {
		$icon = get_term_meta( $term_id, 'lez_termsmeta_icon', true );

		// Bail early if empty
		if ( empty( $icon ) || ! defined( 'LWTV_SYMBOLICONS_PATH' ) ) {
			return;
		}

		if ( ! file_exists( LWTV_SYMBOLICONS_PATH . $icon . '.svg' ) ) {
			$content = 'N/A';
		} else {
			$filename = LWTV_SYMBOLICONS_URL . $icon . '.svg';
			$content  = '<span class="cmb2-icon" role="img">' . file_get_contents( $filename ) . '</span>';
		}

		return $content;
	}

	/*
	 * Create a list of all shows
	 */
	public function cmb2_get_shows_options() {
		$array  = array(
			'post_type'   => 'post_type_shows',
			'numberposts' => wp_count_posts( 'post_type_shows' )->publish,
			'post_status' => array( 'publish', 'pending', 'draft', 'future' ),
		);
		$return = self::get_post_options( $array );
		return $return;
	}

	/*
	 * Create a list of all shows
	 */
	public function cmb2_get_actors_options() {
		return self::get_post_options(
			array(
				'post_type'   => 'post_type_actors',
				'numberposts' => wp_count_posts( 'post_type_actors' )->publish,
				'post_status' => array( 'publish', 'pending', 'draft', 'future' ),
			)
		);
	}

	/**
	 * favorite_shows_user_profile_metabox function.
	 */
	public function favorite_shows_user_profile_metabox() {
		$prefix = 'lez_user_';
		/**
		 * Metabox for the user profile screen
		 */
		$cmb_user = new_cmb2_box(
			array(
				'id'               => $prefix . 'edit',
				'title'            => 'Super Queer Love',
				'object_types'     => array( 'user' ),
				'show_names'       => true,
				'new_user_section' => 'add-new-user',
			)
		);
		$cmb_user->add_field(
			array(
				'name'             => 'Favorite Shows',
				'desc'             => 'pick your favorite shows',
				'id'               => $prefix . 'favourite_shows',
				'type'             => 'select',
				'show_option_none' => true,
				'repeatable'       => true,
				'text'             => array(
					'add_row_text' => 'Add Another Favorite Show',
				),
				'default'          => 'custom',
				'options_cb'       => array( $this, 'cmb2_get_shows_options' ),
				'on_front'         => true,
			)
		);
	}

}

new LWTV_CMB2();
