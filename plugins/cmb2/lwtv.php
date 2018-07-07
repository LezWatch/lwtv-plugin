<?php
/*
Description: Customizations for CMB2
Version: 1.0
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

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'cmb2_admin_init', array( $this, 'favorite_shows_user_profile_metabox' ) );

		$this->icon_taxonomies = array( 'lez_cliches', 'lez_tropes', 'lez_gender', 'lez_sexuality', 'lez_formats', 'lez_genres', 'lez_intersections' );

		// If we don't have symbolicons, there's not a reason to register the taxonomy box...
		if ( defined( 'LP_SYMBOLICONS_PATH' ) ) {
			$this->symbolicon_path = LP_SYMBOLICONS_PATH;
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
	public static function get_post_options( $query_args ) {
		$args = wp_parse_args( $query_args, array(
			'post_type'   => 'post',
			'numberposts' => wp_count_posts( 'post' )->publish,
			'post_status' => array( 'publish' ),
		) );

		$posts = get_posts( $args );

		$post_options = array();
		if ( $posts ) {
			foreach ( $posts as $post ) {
				$post_title = $post->post_title;
				// If we're an actor, we should check for QUEER.
				if ( 'post_type_actors' === $query_args['post_type'] ) {
					if ( get_post_meta( $post->ID, 'lezactors_queer', true ) ) {
						$post_title .= ' (QUEER IRL)';
					}
				}
				// If the post is draft, let's flag it.
				if ( 'draft' === get_post_status( $post->ID ) ) {
					$post_title .= ' - DRAFT';
				}
				$post_options[ $post->ID ] = $post_title;
			}
		}

		// If we're a show, we want to sort and remove stopwords
		if ( 'post_type_shows' === $query_args['post_type'] ) {
			uasort( $post_options, function( $a, $b ) {
				return strnatcasecmp( self::showshort( $a ), self::showshort( $b ) );
			} );
		} else {
			asort( $post_options );
		}

		return $post_options;
	}

	public static function showshort( $str ) {
		list( $first, $rest ) = explode( ' ', $str . ' ', 2 );
		// the extra space is to prevent "undefined offset" notices on single-word titles.
		$validarticles = array( 'a ', 'an ', 'lá ', 'la ', 'las ', 'les ', 'los ', 'el ', 'the ' );
		if ( in_array( strtolower( $first ), $validarticles, true ) ) {
			return $rest . ', ' . $first;
		}
		return $str;
	}

	/**
	 * CSS tweaks
	 *
	 * @access public
	 * @param mixed $hook string - The filename of the page.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		wp_register_style( 'cmb-styles', plugins_url( 'cmb2.css', __FILE__ ) );
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
		$imagepath      = LP_SYMBOLICONS_PATH;
		$icon_array     = array();
		$symbolicon_url = admin_url( 'themes.php?page=symbolicons' );

		foreach ( glob( $imagepath . '*' ) as $filename ) {
			$filename                = str_replace( '.svg', '', str_replace( LP_SYMBOLICONS_PATH, '', $filename ) );
			$icon_array[ $filename ] = $filename;
		}

		$cmb_term = new_cmb2_box( array(
			'id'               => $prefix . 'edit',
			'title'            => 'Category Metabox',
			'object_types'     => array( 'term' ),
			'taxonomies'       => $this->icon_taxonomies,
			'new_term_section' => true,
		) );

		$cmb_term->add_field( array(
			'name'             => 'Icon',
			'desc'             => 'Select the icon you want to use. Once saved, it will show on the left.<br />If you need help visualizing, check out the <a href=' . $symbolicon_url . '>Symbolicons List</a>.',
			'id'               => $prefix . 'icon',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => 'custom',
			'options'          => $icon_array,
			'before_field'     => array( $this, 'before_field_icon' ),
		) );
	}

	// Add before field icon display
	public function before_field_icon( $field_args, $field ) {
		$icon = $field->value;

		// Bail early if empty
		if ( empty( $icon ) || ! defined( 'LP_SYMBOLICONS_PATH' ) ) {
			return;
		}

		if ( ! file_exists( LP_SYMBOLICONS_PATH . $icon . '.svg' ) ) {
			$content = 'N/A';
		} else {
			$filename = LP_SYMBOLICONS_URL . $icon . '.svg';
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
		if ( empty( $icon ) || ! defined( 'LP_SYMBOLICONS_PATH' ) ) {
			return;
		}

		if ( ! file_exists( LP_SYMBOLICONS_PATH . $icon . '.svg' ) ) {
			$content = 'N/A';
		} else {
			$filename = LP_SYMBOLICONS_URL . $icon . '.svg';
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
		$cmb_user = new_cmb2_box( array(
			'id'               => $prefix . 'edit',
			'title'            => 'Super Queer Love',
			'object_types'     => array( 'user' ),
			'show_names'       => true,
			'new_user_section' => 'add-new-user',
		) );
		$cmb_user->add_field( array(
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
		) );
	}

}

new LWTV_CMB2();
