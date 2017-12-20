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
		add_action( 'admin_init', array( $this, 'admin_init') );
		add_action( 'init', array( $this, 'init') );
		add_action( 'init', array( $this, 'create_post_type'), 0 );
		add_action( 'init', array( $this, 'create_taxonomies'), 0 );
		add_action( 'amp_init', array( $this, 'amp_init' ) );
		add_action( 'cmb2_init', array( $this, 'cmb2_metaboxes') );
		add_action( 'admin_menu', array( $this,'remove_metaboxes' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_page_metabox' ) );
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		add_action( 'admin_head', array($this, 'admin_css') );
		add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
		add_action( 'do_update_actor_meta', array( $this, 'update_meta' ), 10, 2 );
		add_action( 'save_post_post_type_characters', array( $this, 'update_meta_from_chars' ), 10, 3 );
	}

	/**
	 *  Init
	 */
	public function init() {
		// Things that only run for this post type
		$post_id   = ( isset( $_GET['post'] ) )? intval( $_GET['post'] ) : 0 ;
		if ( $post_id !== 0 && is_admin() ) {
			$post_type = ( isset( $_GET['post_type'] ) )? sanitize_text_field( $_GET['post_type'] ) : 0 ;
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
	function create_post_type() {
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
		$args = array(
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

		$taxonomies = array (
			'gender'    => 'actor_gender',
			'sexuality' => 'actor_sexuality',
		);

		foreach ( $taxonomies as $pretty => $slug ) {
			// Labels for taxonomy
			$labels = array(
				'name'                       => ucwords( $pretty ) . 's',
				'singular_name'              => ucwords( $pretty ) ,
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
				'show_in_rest'          => true,
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
	 * CMB2 Metaboxes
	 */
	public function cmb2_metaboxes() {
		// prefix for all custom fields
		$prefix = 'lezactors_';

		// Metabox Group: Quick Dropdowns
		$cmb_actorside = new_cmb2_box( array(
			'id'           => 'actors_metabox',
			'title'        => 'Additional Data',
			'object_types' => array( 'post_type_actors' ),
			'context'      => 'side',
			'priority'     => 'default',
			'show_names'   => true, // Show field names on the left
			'show_in_rest' => true,
			'cmb_styles'   => false,
		) );
		// Field: Actor Gender Idenity
		$field_gender = $cmb_actorside->add_field( array(
			'name'             => 'Gender',
			'desc'             => 'Gender Identity',
			'id'               => $prefix . 'gender',
			'taxonomy'         => 'lez_actor_gender',
			'type'             => 'taxonomy_select',
			'default'          => 'cis-woman',
			'show_option_none' => false,
			'remove_default'   => 'true'
		) );
		// Field: Actor Sexual Orientation
		$field_sexuality = $cmb_actorside->add_field( array(
			'name'             => 'Sexuality',
			'desc'             => 'Sexual Orientation',
			'id'               => $prefix . 'sexuality',
			'taxonomy'         => 'lez_actor_sexuality',
			'type'             => 'taxonomy_select',
			'default'          => 'heterosexual',
			'show_option_none' => false,
			'remove_default'   => 'true'
		) );
		// Field: Year of Birth
		$field_birth = $cmb_actorside->add_field( array(
			'name'        => 'Date of Birth',
			'id'          => $prefix . 'birth',
			'type'        => 'text_date',
			'date_format' => 'Y-m-d',
		) );
		// Field: Year of Death (if applicable)
		$field_death = $cmb_actorside->add_field( array(
			'name'        => 'Date of Death',
			'desc'        => 'If applicable.',
			'id'          => $prefix . 'death',
			'type'        => 'text_date',
			'date_format' => 'Y-m-d',
		) );
		// Field: IMDb ID
		$field_imdb = $cmb_actorside->add_field( array(
			'name'       => 'IMDb ID',
			'id'         => $prefix . 'imdb',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'Ex: nm6087250',
			),
		) );
		// Field: WikiPedia
		$field_wiki = $cmb_actorside->add_field( array(
			'name'       => 'WikiPedia URL',
			'id'         => $prefix . 'wikipedia',
			'type'       => 'text_url',
			'attributes' => array(
				'placeholder' => 'https://en.wikipedia.org/wiki/Caity_Lotz',
			),
		) );
		// Actor Sidebar Grid
		if( !is_admin() ){
			return;
		} else {
			$grid_actorside = new \Cmb2Grid\Grid\Cmb2Grid( $cmb_actorside );
			$row1 = $grid_actorside->addRow();
			$row1->addColumns( array( $field_gender, $field_sexuality ) );
		}

	}

	/*
	 * Remove Metaboxes we use elsewhere
	 */
	function remove_metaboxes() {
		remove_meta_box( 'authordiv', 'post_type_actors', 'normal' );
		remove_meta_box( 'postexcerpt' , 'post_type_actors' , 'normal' );
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
	 * This will update the metakey 'lezshows_char_count' on save
	 *
	 * @param int $post_id The post ID.
	 */
	public function update_meta_from_chars( $post_id ) {
		$character_actor_IDs = get_post_meta( $post_id, 'lezchars_actor', true );

		if ( $character_actor_IDs !== '' ) {
			foreach ( $character_actor_IDs as $actor ) {
				do_action( 'do_update_actor_meta' , $actor );
			}
		}
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
				if ( 'post_type_actors' == $post_type ) {
					$text = _n( '%s Actor', '%s Actors', $num_posts->publish );
				}
			$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );
			printf( '<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', $post_type, $text );
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
		</style>";
	}

	/*
	 * Post Page Meta Box
	 * For listing critical information
	 */
	function post_page_metabox() {
		global $post;

		switch ( $post->post_type ) {
			case 'post_type_actors':
				$countqueers = get_post_meta( $post->ID, 'lezactors_char_count', true );
				$deadqueers  = get_post_meta( $post->ID, 'lezactors_dead_count', true );
				echo '<div class="misc-pub-section lwtv misc-pub-lwtv">
					<span id="characters">Characters: <b>' . $countqueers . '</b> total';
						if ( $deadqueers ) { echo '/ <b>' . $deadqueers . '</b> dead'; }
				echo '</span>
				</div>';

				break;
		}
	}

}

// Include Sub Files
include_once( 'actors/calculations.php' );

new LWTV_CPT_Actors();