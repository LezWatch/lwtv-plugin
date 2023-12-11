<?php
/**
 * Lists API
 */

namespace LWTV\Rest_API;

class List_JSON {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Rest API init
	 *
	 * Creates callbacks
	 *   - /lwtv/v1/list/
	 */
	public function rest_api_init() {
		register_rest_route(
			'lwtv/v1',
			'/list/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/list/(?P<type>[a-zA-Z.\-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Rest API Callback
	 */
	public function rest_api_callback( $data ) {
		$params = $data->get_params();
		$type   = ( isset( $params['type'] ) && '' !== $params['type'] ) ? sanitize_title_for_query( $params['type'] ) : 'none';

		if ( ! in_array( $type, array( 'shows', 'characters', 'actors' ), true ) ) {
			$return = new \WP_Error( 'invalid', 'An unexpected error has occurred.' );
		}

		$return = $this->list( $type );
		if ( false === $return ) {
			return new \WP_Error( 'not_found', 'No route was found matching the URL and request method' );
		}

		return $return;
	}

	public function list( $type ) {
		// Get a list of all posts per $type
		$posts = get_posts(
			array(
				'post_type'      => 'post_type_' . $type,
				'posts_per_page' => ( 5 + wp_count_posts( 'post_type_' . $type )->publish ),
				'post_status'    => 'publish',
			)
		);

		// Empty array
		$post_options = array();

		// If there are posts...
		if ( $posts ) {
			foreach ( $posts as $post ) {
				// Base Array
				$post_options[ $post->ID ] = array(
					'title' => $post->post_title,
					'url'   => get_the_permalink( $post->ID ),
				);

				// Custom extra for type
				switch ( $type ) {
					case 'shows':
						$post_options[ $post->ID ]['onair'] = get_post_meta( $post->ID, 'lezshows_on_air', true );
						break;
					case 'actors':
						$post_options[ $post->ID ]['queer'] = ( get_post_meta( $post->ID, 'lezactors_queer', true ) ) ? 'yes' : 'no';
						break;
					case 'characters':
						$post_options[ $post->ID ]['status'] = ( has_term( 'dead', 'lez_cliches', $post->ID ) ) ? 'dead' : 'alive';
						break;
				}
			}
		}

		// Alphabetize
		asort( $post_options );

		return $post_options;
	}
}
