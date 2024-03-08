<?php
/*
 * Registering post meta for Custom Post Types
 *
 * @since 1.0
 */

/**
 * class LWTV_CPTs_Post_Meta
 */

namespace LWTV\CPTs;

class Post_Meta {

	const ALL_POST_META = array(
		// Meta Name                    => Post Type
		// Actors
		'lezactors_birth'               => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_death'               => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_homepage'            => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_facebook'            => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_imdb'                => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_instagram'           => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_mastodon'            => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_tiktok'              => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_tumblr'              => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_twitter'             => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_wikidata_qid'        => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_wikipedia'           => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_char_count'          => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_dead_count'          => array(
			'post_type' => 'post_type_actors',
		),
		'lezactors_show_list'           => array(
			'post_type'  => 'post_type_actors',
			'type'       => 'array',
			'items_type' => 'string',
		),
		'lezactors_saved_wikidata'      => array(
			'post_type'  => 'post_type_actors',
			'type'       => 'object',
			'items_type' => 'string',
		),
		'lezactors_queer_override'      => array(
			'post_type' => 'post_type_actors',
		),
		// Characters
		'lezchars_death_year'           => array(
			'post_type' => 'post_type_characters',
		),
		'lezchars_actor'                => array(
			'post_type' => 'post_type_characters',
		),
		'lezchars_show_group'           => array(
			'post_type' => 'post_type_characters',
		),
		// Shows
		'lezshows_airdates'             => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_affiliate'            => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_char_count'           => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_dead_count'           => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_dead_list'            => array(
			'post_type'  => 'post_type_shows',
			'type'       => 'array',
			'items_type' => 'string',
		),
		'lezshows_imdb'                 => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_episodes'             => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_on_air'               => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_plots'                => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_genres_primary'       => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_quality_details'      => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_quality_rating'       => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_realness_details'     => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_realness_rating'      => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_screentime_rating'    => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_screentime_details'   => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_seasons'              => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_ships'                => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_the_score'            => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_3rd_scores'           => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_tvmaze'               => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_worthit_rating'       => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_worthit_details'      => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_worthit_show_we_love' => array(
			'post_type' => 'post_type_shows',
		),
		'lezshows_similar_shows'        => array(
			'post_type' => 'post_type_shows',
		),
		// TV Maze
		'leztvmaze_our_show'            => array(
			'post_type' => 'post_type_tvmaze',
		),
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'create_meta_data' ), 0 );

		// phpcs:disable
		// Hide taxonomies from Gutenberg.
		// While this isn't the official API for this need, it works.
		// https://github.com/WordPress/gutenberg/issues/6912#issuecomment-428403380
		add_filter( 'rest_prepare_taxonomy', function( $response, $taxonomy ) {

			$all_tax_array = array();
			foreach ( self::ALL_POST_META as $post_meta ) {
				$all_tax_array[] = $post_meta;
			}

			if ( in_array( $taxonomy->name, $all_tax_array ) ) {
				$response->data['visibility']['show_ui'] = false;
			}
			return $response;
		}, 10, 2 );
		// phpcs:enable
	}

	/*
	 * Create and register the meta data for it's associated post type.
	 *
	 * Note: https://make.wordpress.org/core/2019/10/03/wp-5-3-supports-object-and-array-meta-types-in-the-rest-api/
	 */
	public function create_meta_data() {
		$arguments = array(
			'show_in_rest' => true,
		);

		// Register the metas automagically
		foreach ( self::ALL_POST_META as $meta_name => $meta_data ) {
			$post_type = $meta_data['post_type'];

			// Set the type.
			$arguments['type'] = ( isset( $meta_data['type'] ) ) ? $meta_data['type'] : 'string';

			// Set Items Types:
			if ( 'string' !== $arguments['type'] && isset( $meta_data['items_type'] ) ) {
				$arguments['show_in_rest'] = array(
					'schema' => array(
						'type'                 => $meta_data['type'],
						'items'                => array(
							'type' => $meta_data['items_type'],
						),
						'additionalProperties' => array(
							'type' => 'string',
						),
					),
				);

				// Set Properties.
				if ( isset( $meta_data['properties'] ) ) {
					$arguments['show_in_rest']['schema']['items']['properties'] = $meta_data['properties'];
				}
			} else {
				$arguments['show_in_rest'] = true;
			}

			register_post_meta( $post_type, $meta_name, $arguments );
		}
	}
}
