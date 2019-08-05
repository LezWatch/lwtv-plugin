<?php
/*
Description: REST-API: Export

Customized for Wikidata based on conversations
https://docs.google.com/document/d/17CmI01aM0kuOa-YVZxpbX7NoMemutjtu8FPXYGVaVSk/edit
https://tools.wmflabs.org/mix-n-match/import.php

URL will be https://lezwatchtv.com/wp-json/lwtv/v1/export/actor/my-name/

Version: 1.0.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Export_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_Export_JSON {
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
	 *   - /lwtv/v1/of-the-day/
	 */
	public static function rest_api_init() {

		register_rest_route(
			'lwtv/v1',
			'/export/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback' ),
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/export/(?P<type>[a-zA-Z]+)',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback' ),
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/export/(?P<type>[a-zA-Z0-9-]+)/(?P<item>[a-zA-Z0-9-]+)',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback' ),
			)
		);
	}

	/**
	 * Rest API Callback
	 */
	public static function rest_api_callback( $data ) {
		$params = $data->get_params();
		// Type of Custom Post (show, actor, character)
		$type = ( isset( $params['type'] ) && '' !== $params['type'] ) ? sanitize_title_for_query( $params['type'] ) : 'actor';
		// Specific item (actor name etc)
		$item = ( isset( $params['item'] ) && '' !== $params['item'] ) ? sanitize_title_for_query( $params['item'] ) : 'unknown';

		$response = $this->export( $type, $item );
		return $response;
	}

	/*
	 * export function
	 */
	public static function export( $type = 'actor', $item = 'unknown' ) {

		// Sanitize (the switch will check the type)
		$type = sanitize_text_field( $type );
		$item = sanitize_text_field( $item );

		// Create the array
		switch ( $type ) {
			case 'actor':
				$return_array = self::export_actor( $item );
				break;
			case 'character':
				$return_array = self::export_character( $item );
				break;
			case 'show':
				$return_array = self::export_show( $item );
				break;
			case 'list':
				$return_array = self::export_list( $item );
				break;
			default:
				$return_array = '';
				break;
		}

		if ( empty( $return_array ) ) {
			return new WP_Error( 'no_type', 'Invalid content type ( ' . $type . ' ) given.', array( 'status' => 400 ) );
		}

		// No errors! Return array
		return $return_array;
	}


	public static function export_list( $item ) {

		// Default to empty. This will properly error later.
		$return = array();

		if ( in_array( $item, array( 'characters', 'shows', 'actors' ) ) ) {
			$the_loop = LWTV_Loops::post_type_query( 'post_type_' . $item );
			if ( $the_loop->have_posts() ) {
				while ( $the_loop->have_posts() ) {
					$the_loop->the_post();
					$post = get_post();
					$return[] = $post->post_name;
				}
			}
		}

		return $return;

	}

	/**
	 * Export Show
	 *
	 * Export format:
	 * Entry ID: {slug}
	 * Entry Name: {post title}
	 * Entry Description: {lez_formats} airing in {lez_nations} from {lezshows_airdates}
	 *
	 * @access public
	 * @param string $item - name or ID of show
	 * @return array
	 */
	public static function export_show( $item ) {

		// Default to empty. This will properly error later.
		$return = array();

		if ( is_numeric( $item ) ) {
			// If it's numeric, we shall assume it's the post ID.
			$page = get_post( $item );
		} else {
			// Let's get the ID by the title
			$page = get_page_by_path( $item, OBJECT, 'post_type_shows' );

			// If page doesn't exist, let's try by SQL.
			if ( null === $page ) {
				global $wpdb;
				$item_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . esc_sql( $item ) . "'" );
				$page    = get_post( $item_id );
			}
		}
		// Let's make sure.
		if ( isset( $page ) && 'post_type_shows' === get_post_type( $page->ID ) ) {

			// Show Formats
			$format_terms = get_the_terms( $page->ID, 'lez_formats', true );
			if ( $format_terms && ! is_wp_error( $format_terms ) ) {
				$format_string = join( ', ', wp_list_pluck( $format_terms, 'name' ) );
			}

			// Nations
			$nation_terms = get_the_terms( $page->ID, 'lez_country', true );
			if ( $nation_terms && ! is_wp_error( $nation_terms ) ) {
				$nation_array = wp_list_pluck( $nation_terms, 'name' );
			}
			if ( isset( $nation_array ) ) {
				if ( count( $nation_array ) > 1 ) {
					$last_element = array_pop( $nation_array );
					array_push( $nation_array, 'and ' . $last_element );
				}
				$nation_string = implode( ', ', $nation_array );
			}

			// Airdates
			$airdates = get_post_meta( $page->ID, 'lezshows_airdates', true );
			if ( $airdates ) {
				$airdates['finish'] = ( 'current' === $airdates['finish'] ) ? 'now' : $airdates['finish'];
				$dates              = $airdates['start'] . '-' . $airdates['finish'];
				if ( $airdates['start'] === $airdates['finish'] ) {
					$dates = $airdates['finish'];
				}
			}

			// Build the description
			$description = array(
				'formats' => isset( $format_string ) ? $format_string : '',
				'nations' => isset( $nation_string ) ? $nation_string : '',
				'dates'   => isset( $dates ) ? $dates : '',
			);

			$return = array(
				'uid'  => $page->ID,
				'id'   => $page->post_name,
				'name' => $page->post_title,
				'description' => $description['formats'] . ' airing in ' . $description['nations'] . ' from ' . $description['dates'] . '.',
			);
		}

		return $return;
	}

	/**
	 * Export Character
	 *
	 * Export format:
	 * Entry ID: {slug}
	 * Entry Name: {post title}
	 * Entry Description: A {lezchars_sexuality} {lezchars_gender} character on {*show(s)}. Played by {actor}.
	 *
	 * @access public
	 * @param string $item - name or ID of character
	 * @return array
	 */
	public static function export_character( $item ) {

		// Default to empty. This will properly error later.
		$return = array();

		if ( is_numeric( $item ) ) {
			// If it's numeric, we shall assume it's the post ID.
			$page = get_post( $item );
		} else {
			// Let's get the ID by the title
			$page = get_page_by_path( $item, OBJECT, 'post_type_characters' );

			// If page doesn't exist, let's try by SQL.
			if ( null === $page ) {
				global $wpdb;
				$item_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . esc_sql( $item ) . "'" );
				$page    = get_post( $item_id );
			}
		}
		// Let's make sure.
		if ( isset( $page ) && 'post_type_characters' === get_post_type( $page->ID ) ) {

			// Sexuality
			$sexuality_terms = get_the_terms( $page->ID, 'lez_sexuality', true );
			if ( $sexuality_terms && ! is_wp_error( $sexuality_terms ) ) {
				$sexuality_string = join( ', ', wp_list_pluck( $sexuality_terms, 'name' ) );
			}

			// Gender
			$gender_terms = get_the_terms( $page->ID, 'lez_gender', true );
			if ( $gender_terms && ! is_wp_error( $gender_terms ) ) {
				$gender_string = implode( ', ', wp_list_pluck( $gender_terms, 'name' ) );
				$gender_string = ( 'Cisgender' === $gender_string ) ? 'Cisgender Female' : $gender_string;
			}

			// Shows
			$all_shows = get_post_meta( $page->ID, 'lezchars_show_group', true );
			if ( '' !== $all_shows ) {
				foreach ( $all_shows as $a_show ) {
					$the_shows[] = '"' . get_the_title( $a_show['show'] ) . '"';
				}

				if ( isset( $the_shows ) ) {
					if ( count( $the_shows ) > 1 ) {
						$last_element = array_pop( $the_shows );
						array_push( $the_shows, 'and ' . $last_element );
					}
					$show = implode( ', ', $the_shows );
				}
			}

			// Characters
			$all_chars = get_post_meta( $page->ID, 'lezchars_actor', true );
			if ( ! is_array( $all_chars ) ) {
				$all_chars = array( get_post_meta( $page->ID, 'lezchars_actor', true ) );
			}
			foreach ( $all_chars as $a_char ) {
				$the_chars[] = get_the_title( $a_char );
			}
			if ( isset( $the_chars ) ) {
				if ( count( $the_chars ) > 1 ) {
					$last_element = array_pop( $the_chars );
					array_push( $the_chars, 'and ' . $last_element );
				}
				$character = implode( ', ', $the_chars );
			}

			// Build the description
			$description = array(
				'sexuality' => isset( $sexuality_string ) ? $sexuality_string : '',
				'gender'    => isset( $gender_string ) ? $gender_string : '',
				'show'      => isset( $show ) ? $show : '',
				'character' => isset( $character ) ? $character : '',
			);

			$return = array(
				'uid'  => $page->ID,
				'id'   => $page->post_name,
				'name' => $page->post_title,
				'description' => 'A ' . $description['sexuality'] . ' ' . $description['gender'] . ' character on' . $description['show'] . '. Played by ' . $description['character'] . '.',
			);
		}

		return $return;
	}

	/**
	 * Export Actor
	 *
	 * Export format:
	 * Entry ID: {slug}
	 * Entry Name: {post title}
	 * Entry Description: {lez_actor_sexuality} {lez_actor_gender} actor b. {lezactors_birth} and died {lezactors_death}. {lezactors_wikipedia}
	 *
	 * @access public
	 * @param string $item - name or ID of actor
	 * @return array
	 */
	public static function export_actor( $item ) {

		// Default to empty. This will properly error later.
		$return = array();

		if ( is_numeric( $item ) ) {
			// If it's numeric, we shall assume it's the post ID.
			$page = get_post( $item );
		} else {
			// Let's get the ID by the title
			$page = get_page_by_path( $item, OBJECT, 'post_type_actors' );

			// If page doesn't exist, let's try by SQL.
			if ( null === $page ) {
				global $wpdb;
				$item_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . esc_sql( $item ) . "'" );
				$page    = get_post( $item_id );
			}
		}

		// Let's make sure.
		if ( isset( $page ) && 'post_type_actors' === get_post_type( $page->ID ) ) {

			// Sexuality
			$sexuality_terms = get_the_terms( $page->ID, 'lez_actor_sexuality', true );
			if ( $sexuality_terms && ! is_wp_error( $sexuality_terms ) ) {
				$sexuality_string = join( ', ', wp_list_pluck( $sexuality_terms, 'name' ) );
			}

			// Gender
			$gender_terms = get_the_terms( $page->ID, 'lez_actor_gender', true );
			if ( $gender_terms && ! is_wp_error( $gender_terms ) ) {
				$gender_string = join( ', ', wp_list_pluck( $gender_terms, 'name' ) );
			}

			// Born
			if ( get_post_meta( $page->ID, 'lezactors_birth', true ) ) {
				$get_birth = new DateTime( get_post_meta( $page->ID, 'lezactors_birth', true ) );
				$born      = ' b. ' . date_format( $get_birth, 'F d, Y' );
			}

			// Died
			if ( get_post_meta( $page->ID, 'lezactors_death', true ) ) {
				$get_dead = new DateTime( get_post_meta( $page->ID, 'lezactors_death', true ) );
				$died     = ' and died ' . date_format( $get_dead, 'F d, Y' );
			}

			if ( get_post_meta( $page->ID, 'lezactors_wikipedia', true ) ) {
				$wikipedia = ' ' . esc_url( get_post_meta( $page->ID, 'lezactors_wikipedia', true ) );
			}

			$description = array(
				'sexuality' => isset( $sexuality_string ) ? $sexuality_string : '',
				'gender'    => isset( $gender_string ) ? $gender_string : '',
				'born'      => isset( $born ) ? $born : '',
				'died'      => isset( $died ) ? $died : '',
				'wikipedia' => isset( $wikipedia ) ? $wikipedia : '',
			);

			$return = array(
				'uid'  => $page->ID,
				'id'   => $page->post_name,
				'name' => $page->post_title,
				'description' => $description['sexuality'] . ' ' . $description['gender'] . ' actor' . $description['born'] . $description['died'] . '.' . $description['wikipedia'],
			);
		}

		return $return;

	}

}

new LWTV_Export_JSON();
