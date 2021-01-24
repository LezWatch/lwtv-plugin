<?php
/*
Description: REST-API - List output

So other people can get lists

- Shows
- Characters

Version: 1.2
Author: Mika Epstein
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Lists_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_Lists_JSON {
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
	 *   - /lwtv/v1/lists/[characters]/[gender|sexuality]/[non-binary|asexual]
	 *
	 * Type  : Post Type
	 * Group : Taxonomy (gender, sexuality)
	 * Term  : Term (non-binary, queer, asexual)
	 */
	public function rest_api_init() {

		// Basic
		register_rest_route(
			'lwtv/v1',
			'/lists/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		// Post Types
		register_rest_route(
			'lwtv/v1',
			'/lists/(?P<type>[a-zA-Z.\-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		// Post Type AND Group
		register_rest_route(
			'lwtv/v1',
			'/lists/(?P<type>[a-zA-Z]+)/(?P<group>[a-zA-Z]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		// Post Type AND Group AND Term
		register_rest_route(
			'lwtv/v1',
			'/lists/(?P<type>[a-zA-Z]+)/(?P<group>[a-zA-Z]+)/(?P<term>[a-zA-Z]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Rest API Callback
	 *
	 * @access public
	 * @param mixed $data - string.
	 * @return array
	 */
	public function rest_api_callback( $data ) {
		$params = $data->get_params();
		$type   = ( isset( $params['type'] ) && '' !== $params['type'] ) ? sanitize_title_for_query( $params['type'] ) : 'character';
		$group  = ( isset( $params['group'] ) && '' !== $params['group'] ) ? sanitize_title_for_query( $params['group'] ) : 'none';
		$term   = ( isset( $params['term'] ) && '' !== $params['term'] ) ? sanitize_title_for_query( $params['term'] ) : 'none';

		// Get Response
		$response = $this->response( $type, $group, $term );

		if ( false === $response ) {
			return new WP_Error( 'not_found', 'No route was found matching the URL and request method. Sorry.' );
		}

		return $response;
	}

	/**
	 * Generate Lists
	 *
	 * @return array with stats data
	 */
	public function response( $type, $group, $term ) {

		// Remove <!--fwp-loop--> from output
		// phpcs:ignore
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

		// Prep Return
		$response_array = array();

		// Valid Data
		$valid_type  = array( 'characters' );
		$valid_group = array( 'cliches', 'gender', 'sexuality', 'romantic' );

		// If it's not in the array, we have to fail.
		if ( ! in_array( $type, $valid_type, true ) ) {
			return new WP_Error( 'not_found', 'Listing only supports "characters" at this time.' );
		}

		if ( 'none' === $group ) {
			// If there is no group, we list the groups we allow and their counts
			$term_params = array(
				'hide_empty' => false,
				'parent'     => 0,
			);

			foreach ( $valid_group as $this_group ) {
				$response_array[ $this_group ] = wp_count_terms( 'lez_' . $this_group, $term_params );
			}
		} elseif ( ! in_array( $group, $valid_group, true ) ) {
			// Failure
			return new WP_Error( 'not_found', 'No route was found matching the URL and request method: ' . $group );
		} else {
			if ( 'none' === $term ) {
				// Make list of all terms
				$terms = get_terms(
					array(
						'taxonomy' => 'lez_' . $group,
					),
				);

				// Process list to show term slug and count
				foreach ( $terms as $one_term ) {
					$response_array[ $one_term->slug ] = $one_term->count;
				}
			} else {
				$response_array = self::get_list( $type, $group, $term );
			}
		}

		return $response_array;
	}

	/**
	 * get_list function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_list( $type, $group, $term ) {
		switch ( $type ) {
			case 'character':
			case 'characters':
				$response = self::characters( $group, $term );
				break;
		}

		if ( ! isset( $response ) ) {
			return new WP_Error( 'not_found', 'Currently we can only list data on characters. The rest is coming soon.' );
		} else {
			return $response;
		}
	}

	/**
	 * characters function.
	 *
	 * @access public
	 * @return array
	 */
	public function characters( $group, $term ) {
		$the_loop   = ( new LWTV_Loops() )->tax_query( 'post_type_characters', 'lez_' . $group, 'slug', $term );
		$characters = array();

		if ( $the_loop->have_posts() ) {
			while ( $the_loop->have_posts() ) {
				$the_loop->the_post();
				$post = get_post();

				// Gender -- array of all applicable
				$gender       = array();
				$gender_terms = get_the_terms( $post->ID, 'lez_gender', true );
				if ( $gender_terms && ! is_wp_error( $gender_terms ) ) {
					foreach ( $gender_terms as $gender_term ) {
						$gender[] = $gender_term->name;
					}
				}

				// Sexuality -- array of all applicable
				$sexuality       = array();
				$sexuality_terms = get_the_terms( $post->ID, 'lez_gender', true );
				if ( $sexuality_terms && ! is_wp_error( $sexuality_terms ) ) {
					foreach ( $sexuality_terms as $sexuality_term ) {
						$sexuality[] = $sexuality_term->name;
					}
				}

				// Cliches -- array of all applicable
				$cliches     = array();
				$lez_cliches = get_the_terms( $post->ID, 'lez_cliches' );
				if ( $lez_cliches && ! is_wp_error( $lez_cliches ) ) {
					foreach ( $lez_cliches as $the_cliche ) {
						$cliches[] = $the_cliche->name;
					}
				}

				// Shows
				$shows_full  = get_post_meta( $post->ID, 'lezchars_show_group', true );
				$shows_clean = array();
				foreach ( $shows_full as $show ) {
					$shows_clean[] = array(
						'name'     => get_the_title( $show['show'] ),
						'chartype' => $show['type'],
						'years'    => $show['appears'],
					);
				}

				$characters[ $post->post_name ] = array(
					'id'        => $post->ID,
					'name'      => $post->post_title,
					'sexuality' => $sexuality,
					'gender'    => $gender,
					'cliches'   => $cliches,
					'shows'     => $shows_clean,
					'url'       => home_url( '/character/' ) . $post->post_name,
				);
			}

			wp_reset_query();
		}

		if ( ! isset( $characters ) ) {
			return new WP_Error( 'not_found', 'No route was found matching the URL and request method: ' . $term );
		} else {
			return $characters;
		}
	}
}

new LWTV_Lists_JSON();
