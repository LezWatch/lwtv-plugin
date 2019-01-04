<?php
/*
 * Registering metaboxes for Custom Post Types
 *
 * @since 1.0
 */

/**
 * class LWTV_CPT_Post_Meta
 */
class LWTV_CPT_Post_Meta {

	protected static $all_post_metas;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'create_meta_data' ), 0 );

		self::$all_post_metas = array(
			// Meta Name                    => Post Type
			'lezactors_birth'               => 'post_type_actors',
			'lezactors_death'               => 'post_type_actors',
			'lezactors_imdb'                => 'post_type_actors',
			'lezactors_wikipedia'           => 'post_type_actors',
			'lezactors_homepage'            => 'post_type_actors',
			'lezactors_twitter'             => 'post_type_actors',
			'lezactors_instagram'           => 'post_type_actors',
			'lezchars_death_year'           => 'post_type_characters',
			'lezchars_actor'                => 'post_type_characters',
			'lezchars_show_group'           => 'post_type_characters',
			'lezshows_airdates'             => 'post_type_shows',
			'lezshows_seasons'              => 'post_type_shows',
			'lezshows_tvtype'               => 'post_type_shows',
			'lezshows_imdb'                 => 'post_type_shows',
			'lezshows_worthit_rating'       => 'post_type_shows',
			'lezshows_worthit_details'      => 'post_type_shows',
			'lezshows_worthit_show_we_love' => 'post_type_shows',
			'lezshows_affiliate'            => 'post_type_shows',
			'lezshows_similar_shows'        => 'post_type_shows',
			'lezshows_ships'                => 'post_type_shows',
			'lezshows_plots'                => 'post_type_shows',
			'lezshows_episodes'             => 'post_type_shows',
			'lezshows_realness_rating'      => 'post_type_shows',
			'lezshows_realness_details'     => 'post_type_shows',
			'lezshows_quality_rating'       => 'post_type_shows',
			'lezshows_quality_details'      => 'post_type_shows',
			'lezshows_screentime_rating'    => 'post_type_shows',
			'lezshows_screentime_details'   => 'post_type_shows',
		);

		// phpcs:disable
		// Hide taxonomies from Gutenberg.
		// While this isn't the official API for this need, it works.
		// https://github.com/WordPress/gutenberg/issues/6912#issuecomment-428403380
		add_filter( 'rest_prepare_taxonomy', function( $response, $taxonomy ) {

			$all_tax_array = array();
			foreach ( self::$all_post_metas as $post_meta => $post_type ) {
				$all_tax_array[] = $post_meta;
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
		add_filter( 'manage_post_type_characters_posts_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_post_type_characters_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'manage_edit-post_type_characters_sortable_columns', array( $this, 'manage_edit_sortable_columns' ) );

		add_filter( 'posts_clauses', array( $this, 'columns_sortability_sexuality' ), 10, 2 );
		add_filter( 'posts_clauses', array( $this, 'columns_sortability_gender' ), 10, 2 );
		add_filter( 'posts_clauses', array( $this, 'columns_sortability_romantic' ), 10, 2 );
	}

	/*
	 * Create and register the meta data for this post type
	 */
	public function create_meta_data() {
		$default_args = array(
			'show_in_rest' => true,
		);

		// Register the metas automagically
		foreach ( self::$all_post_metas as $meta_name => $post_type ) {
			register_meta( $post_type, $meta_name, $default_args );
		}

	}
}

new LWTV_CPT_Post_Meta();
