<?php
/*
Description: REST-API: Export

Customized for Wikidata based on conversations
https://docs.google.com/document/d/17CmI01aM0kuOa-YVZxpbX7NoMemutjtu8FPXYGVaVSk/edit
https://tools.wmflabs.org/mix-n-match/import.php

URL will be https://lezwatchtv.com/wp-json/lwtv/v1/export/actor/my-name/

Version: 1.1.1
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
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Rest API init
	 *
	 * Creates callbacks
	 *   - /lwtv/v1/export/
	 *
	 * Doc: https://docs.lezwatchtv.com/api/global/export/
	 *
	 * @since 1.0
	 */
	public function rest_api_init() {

		register_rest_route(
			'lwtv/v1',
			'/export/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'lwtv/v1',
			'/export/(?P<type>[a-zA-Z]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/export/(?P<type>[a-zA-Z0-9-]+)/(?P<item>[a-zA-Z0-9-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/export/(?P<type>[a-zA-Z0-9-]+)/(?P<item>[a-zA-Z0-9-]+)/(?P<tax>[a-zA-Z0-9-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/export/(?P<type>[a-zA-Z0-9-]+)/(?P<item>[a-zA-Z0-9-]+)/(?P<tax>[a-zA-Z0-9-]+)/(?P<term>[a-zA-Z0-9-]+)',
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
	 * @since 1.0
	 */
	public function rest_api_callback( $data ) {
		$params = $data->get_params();

		// Type of Custom Post (show, actor, character)
		$type = ( isset( $params['type'] ) && '' !== $params['type'] ) ? sanitize_title_for_query( $params['type'] ) : 'actor';

		// Specific item (actor name etc)
		$item = ( isset( $params['item'] ) && '' !== $params['item'] ) ? sanitize_title_for_query( $params['item'] ) : 'unknown';

		// Taxonomy
		$tax = ( isset( $params['tax'] ) && '' !== $params['tax'] ) ? sanitize_title_for_query( $params['tax'] ) : 'none';

		// Term
		$term = ( isset( $params['term'] ) && '' !== $params['term'] ) ? sanitize_title_for_query( $params['term'] ) : 'none';

		$response = $this->export( $type, $item, $tax, $term );

		return $response;
	}

	/*
	 * export function
	 */
	public function export( $type = 'actor', $item = 'unknown', $tax = '', $term = '' ) {

		// Sanitize (the switch will check the type)
		$type = sanitize_text_field( $type );
		$item = sanitize_text_field( $item );
		$tax  = sanitize_text_field( $tax );
		$term = sanitize_text_field( $term );

		// Create the array
		switch ( $type ) {
			case 'actor':
			case 'actors':
				$return_array = self::export_actor( $item );
				break;
			case 'character':
			case 'characters':
				$return_array = self::export_character( $item );
				break;
			case 'show':
			case 'shows':
				$return_array = self::export_show( $item );
				break;
			case 'list':
			case 'wiki':
				$return_array = self::export_list( $item );
				break;
			case 'raw':
				$return_array = self::export_raw( $item );
				break;
			case 'full':
				$return_array = self::export_full( $item, $tax, $term );
				break;
			default:
				$return_array = '';
				break;
		}

		if ( empty( $return_array ) ) {
			return new WP_Error( 'no_type', 'Invalid content type (' . $type . ') or name (' . $item . ') given.', array( 'status' => 400 ) );
		}

		// No errors! Return array
		return $return_array;
	}

	/**
	 * Export list of all data with basic output
	 * @param  string $item characters, actors, or shows.
	 * @return array        json array.
	 */
	public function export_list( $item ) {

		// Default to empty. This will properly error later.
		$return = array();

		if ( in_array( $item, array( 'characters', 'shows', 'actors' ), true ) ) {
			$the_loop = ( new LWTV_Loops() )->post_type_query( 'post_type_' . $item );
			if ( $the_loop->have_posts() ) {
				while ( $the_loop->have_posts() ) {
					$the_loop->the_post();
					$post = get_post();

					switch ( $item ) {
						case 'actor':
						case 'actors':
							$return[] = self::export_actor( $post->post_name, 'wiki' );
							break;
						case 'character':
						case 'characters':
							$return[] = self::export_character( $post->post_name, 'wiki' );
							break;
						case 'show':
						case 'shows':
							$return[] = self::export_show( $post->post_name, 'wiki' );
							break;
					}
				}
			}
		}
		return $return;
	}

	/**
	 * Export list of all data in  more raw way.
	 * @param  string $item actors or shows.
	 * @return array        json array.
	 */
	public function export_raw( $item ) {

		// Default to empty. This will properly error later.
		$return = array();

		if ( in_array( $item, array( 'characters', 'shows', 'actors' ), true ) ) {
			$the_loop = ( new LWTV_Loops() )->post_type_query( 'post_type_' . $item );
			if ( $the_loop->have_posts() ) {
				while ( $the_loop->have_posts() ) {
					$the_loop->the_post();
					$post = get_post();

					switch ( $item ) {
						case 'actor':
						case 'actors':
							$return[] = self::export_actor( $post->post_name, 'raw' );
							break;
						case 'character':
						case 'characters':
							$return[] = self::export_character( $post->post_name, 'raw' );
							break;
						case 'show':
						case 'shows':
							$return[] = self::export_show( $post->post_name, 'raw' );
							break;
					}
				}
			}
		}
		return $return;
	}

	/**
	 * Exports lists of data with more details, based on specific.
	 * @param  string $item actors or shows.
	 * @return array        json array.
	 */
	public function export_full( $item, $tax, $term ) {

		// Remove <!--fwp-loop--> from output
		add_filter(
			'facetwp_is_main_query',
			function( $is_main_query, $query ) {
				return false;
			},
			10,
			2
		);

		// Prep Return
		$response_array = array();

		// Valid Data
		$valid_item = array( 'characters' );
		$valid_tax  = array( 'cliches', 'gender', 'sexuality', 'romantic' );

		// If it's not in the array, we have to fail.
		if ( ! in_array( $item, $valid_item, true ) ) {
			return new WP_Error( 'not_found', 'Full listing only supports "characters" at this time.' );
		}

		if ( ! isset( $tax ) || 'none' === $tax ) {
			// If there is no Taxonomy, we list the groups we allow and their counts
			$term_params = array(
				'hide_empty' => false,
				'parent'     => 0,
			);

			foreach ( $valid_tax as $this_tax ) {
				$response_array[ $this_tax ] = wp_count_terms( 'lez_' . $this_tax, $term_params );
			}
		} elseif ( ! in_array( $tax, $valid_tax, true ) ) {
			// Failure
			return new WP_Error( 'not_found', 'No route was found matching the URL and request method: ' . $tax );
		} else {
			if ( ! isset( $term ) || 'none' === $term ) {
				// Make list of all terms
				$terms = get_terms(
					array(
						'taxonomy' => 'lez_' . $tax,
					),
				);

				// Process list to show term slug and count
				foreach ( $terms as $one_term ) {
					$response_array[ $one_term->slug ] = $one_term->count;
				}
			} else {
				$response_array = self::get_full_list( $item, $tax, $term );
			}
		}

		return $response_array;

	}

	/**
	 * get_full_list function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_full_list( $type, $group, $term ) {
		switch ( $type ) {
			case 'character':
			case 'characters':
				$response = self::get_full_list_characters( $group, $term );
				break;
		}

		if ( ! isset( $response ) ) {
			return new WP_Error( 'not_found', 'Currently we can only list data on characters. The rest is coming soon.' );
		} else {
			return $response;
		}
	}

	/**
	 * get_full_list_characters function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_full_list_characters( $group, $term ) {
		$the_loop = ( new LWTV_Loops() )->tax_query( 'post_type_characters', 'lez_' . $group, 'slug', $term );

		if ( $the_loop->have_posts() ) {
			$characters_list = wp_list_pluck( $the_loop->posts, 'ID' );
			wp_reset_query();
		}

		// Make empty array for later
		$characters = array();

		foreach ( $characters_list as $character ) {
			// Gender -- array of all applicable
			$gender       = array();
			$gender_terms = get_the_terms( $character, 'lez_gender', true );
			if ( $gender_terms && ! is_wp_error( $gender_terms ) ) {
				foreach ( $gender_terms as $gender_term ) {
					$gender[] = $gender_term->name;
				}
			}

			// Sexuality -- array of all applicable
			$sexuality       = array();
			$sexuality_terms = get_the_terms( $character, 'lez_sexuality', true );
			if ( $sexuality_terms && ! is_wp_error( $sexuality_terms ) ) {
				foreach ( $sexuality_terms as $sexuality_term ) {
					$sexuality[] = $sexuality_term->name;
				}
			}

			// Cliches -- array of all applicable
			$cliches     = array();
			$lez_cliches = get_the_terms( $character, 'lez_cliches' );
			if ( $lez_cliches && ! is_wp_error( $lez_cliches ) ) {
				foreach ( $lez_cliches as $the_cliche ) {
					$cliches[] = $the_cliche->name;
				}
			}
			$cliches_clean = implode( '; ', $cliches );

			// Shows
			$shows_full  = get_post_meta( $character, 'lezchars_show_group', true );
			$shows_array = array();
			foreach ( $shows_full as $show ) {
				// Remove the Array.
				if ( is_array( $show['show'] ) ) {
					$show['show'] = $show['show'][0];
				}

				$appears       = implode( ';', $show['appears'] );
				$shows_array[] = get_the_title( $show['show'] ) . ' - ' . $show['type'] . ' (' . $appears . ')';
			}
			$shows_clean = implode( '; ', $shows_array );

			$characters[ $post->post_name ] = array(
				'id'        => $post->ID,
				'name'      => $post->post_title,
				'url'       => home_url( '/character/' ) . $post->post_name,
				'image'     => wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ),
				'sexuality' => $sexuality,
				'gender'    => $gender,
				'cliches'   => $cliches_clean,
				'shows'     => $shows_clean,
			);
		}

		if ( ! isset( $characters ) || empty( $characters ) ) {
			return new WP_Error( 'not_found', 'No route was found matching the URL and request method: ' . $term );
		} else {
			return $characters;
		}
	}

	/**
	 * Export Show
	 *
	 * Export format:
	 * Entry ID: {slug}
	 * Entry Name: {post title}
	 * Entry Description: {lez_formats} airing in {lez_nations} from {lezshows_airdates}
	 * Links: {IMDB}
	 *
	 * @access public
	 * @param string $item - name or ID of show
	 * @param string $format - plain or detailed
	 * @return array
	 * @since 1.0
	 */
	public function export_show( $item, $format = 'wiki' ) {

		// Default to empty. This will properly error later.
		$return = array();

		if ( is_numeric( $item ) ) {
			// If it's numeric, we shall assume it's the post ID.
			$page = get_post( $item );
		} else {
			// Let's get the ID by the title
			$page = get_page_by_path( $item, OBJECT, 'post_type_shows' );
		}

		// If page doesn't exist, let's try by SQL.
		if ( ! isset( $page ) || null === $page ) {
			global $wpdb;
			$item_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . esc_sql( $item ) . "'" );
			$page    = get_post( $item_id );
		}

		// Let's make sure.
		if ( isset( $page ) && 'post_type_shows' === get_post_type( $page->ID ) ) {

			// Empty to start
			$data = array();

			// Basic data
			$return = array(
				'uid'  => $page->ID,
				'id'   => $page->post_name,
				'name' => $page->post_title,
			);

			// Show Formats
			$format_terms    = get_the_terms( $page->ID, 'lez_formats', true );
			$data['formats'] = ( $format_terms && ! is_wp_error( $format_terms ) ) ? join( ', ', wp_list_pluck( $format_terms, 'name' ) ) : '';

			// Nations
			$data['nations'] = '';
			$nation_terms    = get_the_terms( $page->ID, 'lez_country', true );
			$nation_array    = ( $nation_terms && ! is_wp_error( $nation_terms ) ) ? wp_list_pluck( $nation_terms, 'name' ) : '';
			if ( is_array( $nation_array ) ) {
				if ( count( $nation_array ) > 1 && 'wiki' === $format ) {
					$last_element = array_pop( $nation_array );
					array_push( $nation_array, 'and ' . $last_element );
				}
				$data['nations'] = implode( ', ', $nation_array );
			}

			// Stations
			$station_terms    = get_the_terms( $page->ID, 'lez_stations', true );
			$data['stations'] = ( $station_terms && ! is_wp_error( $station_terms ) ) ? join( ', ', wp_list_pluck( $station_terms, 'name' ) ) : '';

			// Airdates
			$airdates = get_post_meta( $page->ID, 'lezshows_airdates', true );
			if ( $airdates ) {
				$airdates['finish']  = ( 'current' === $airdates['finish'] ) ? 'now' : $airdates['finish'];
				$data['dates_raw']   = $airdates['start'] . '-' . $airdates['finish'];
				$data['dates_plain'] = 'from ' . $data['dates_raw'];
				if ( $airdates['start'] === $airdates['finish'] ) {
					$data['dates_raw']   = $airdates['finish'];
					$data['dates_plain'] = 'in ' . $data['dates_raw'];
				}
			}

			// IMDB
			$data['imdb'] = ( get_post_meta( $page->ID, 'lezshows_imdb', true ) ) ? 'https://imdb.com/title/' . get_post_meta( $page->ID, 'lezshows_imdb', true ) : '';

			switch ( $format ) {
				case 'wiki':
					$return['description'] = $data['formats'] . ' airing in ' . $data['nations'] . ' ' . $data['dates_plain'] . '.';
					break;
				case 'raw':
					$return['format']   = $data['formats'];
					$return['nation']   = $data['nations'];
					$return['airdates'] = $data['dates_raw'];
					$return['stations'] = $data['stations'];
					$return['imdb']     = $data['imdb'];
			}
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
	 * Links: {N/A -- there are no extra links.}
	 *
	 * @access public
	 * @param string $item - name or ID of character
	 * @return array
	 * @since 1.0
	 */
	public function export_character( $item, $format = 'wiki' ) {

		// Default to empty. This will properly error later.
		$return = array();

		if ( is_numeric( $item ) ) {
			// If it's numeric, we shall assume it's the post ID.
			$page = get_post( $item );
		} else {
			// Let's get the ID by the title
			$page = get_page_by_path( $item, OBJECT, 'post_type_characters' );
		}

		// If page doesn't exist, let's try by SQL.
		if ( ! isset( $page ) || null === $page ) {
			global $wpdb;
			$item_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . esc_sql( $item ) . "'" );
			$page    = get_post( $item_id );
		}

		// Let's make sure.
		if ( isset( $page ) && 'post_type_characters' === get_post_type( $page->ID ) ) {

			// Basic Data we always need
			$return = array(
				'uid'  => $page->ID,
				'id'   => $page->post_name,
				'name' => $page->post_title,
			);

			$data = array();

			// Sexuality
			$sexuality_terms   = get_the_terms( $page->ID, 'lez_sexuality', true );
			$data['sexuality'] = ( $sexuality_terms && ! is_wp_error( $sexuality_terms ) ) ? join( ', ', wp_list_pluck( $sexuality_terms, 'name' ) ) : '';

			// Gender
			$gender_terms = get_the_terms( $page->ID, 'lez_gender', true );
			if ( $gender_terms && ! is_wp_error( $gender_terms ) ) {
				$gender_string = implode( ', ', wp_list_pluck( $gender_terms, 'name' ) );
				$gender_string = ( 'Cisgender' === $gender_string ) ? 'Cisgender Female' : $gender_string;
			}
			$data['gender'] = ( isset( $gender_string ) ) ? $gender_string : '';

			// Shows
			$all_shows = get_post_meta( $page->ID, 'lezchars_show_group', true );
			if ( '' !== $all_shows ) {
				foreach ( $all_shows as $a_show ) {
					// Remove the Array.
					if ( is_array( $a_show['show'] ) ) {
						$a_show['show'] = $a_show['show'][0];
					}

					$the_shows[] = '\'' . get_the_title( $a_show['show'] ) . '\'';
				}

				if ( isset( $the_shows ) ) {
					if ( count( $the_shows ) > 1 && 'wiki' === $format ) {
						$last_element = array_pop( $the_shows );
						array_push( $the_shows, 'and ' . $last_element );
					}
					$show = implode( ', ', $the_shows );
				}
			}
			$data['show'] = ( isset( $show ) ) ? $show : '';

			// Actors
			$all_actors = get_post_meta( $page->ID, 'lezchars_actor', true );
			if ( ! is_array( $all_actors ) ) {
				$all_actors = array( get_post_meta( $page->ID, 'lezchars_actor', true ) );
			}
			foreach ( $all_actors as $an_actor ) {
				$the_actors[] = get_the_title( $an_actor );
			}
			if ( isset( $the_actors ) ) {
				if ( count( $the_actors ) > 1 && 'wiki' === $format ) {
					$last_element = array_pop( $the_actors );
					array_push( $the_actors, 'and ' . $last_element );
				}
				$actor = implode( ', ', $the_actors );
			}
			$data['actor'] = ( isset( $actor ) ) ? $actor : '';

			switch ( $format ) {
				case 'wiki':
					$return['description'] = 'A ' . $data['sexuality'] . ' ' . $data['gender'] . ' character on ' . $data['show'] . '. Played by ' . $data['actor'] . '.';
					break;
				case 'raw':
					$return['sexuality'] = $data['sexuality'];
					$return['gender']    = $data['gender'];
					$return['show']      = $data['show'];
					$return['actor']     = $data['actor'];
					break;
			}
		}

		return $return;
	}

	/**
	 * Export Actor
	 *
	 * Export format:
	 * Entry ID: {slug}
	 * Entry Name: {post title}
	 * Entry Description (plain): {lez_actor_sexuality} {lez_actor_gender} actor b. {lezactors_birth} and died {lezactors_death}. {lezactors_wikipedia}
	 * Detailed: {twitter, instagram, etc}
	 *
	 * @access public
	 * @param string $item - name or ID of actor
	 * @return array
	 * @since 1.0
	 */
	public function export_actor( $item, $format = 'wiki' ) {

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

			// Empty Array
			$data = array();

			// Sexuality
			$sexuality_terms   = get_the_terms( $page->ID, 'lez_actor_sexuality', true );
			$data['sexuality'] = ( $sexuality_terms && ! is_wp_error( $sexuality_terms ) ) ? join( ', ', wp_list_pluck( $sexuality_terms, 'name' ) ) : '';

			// Gender
			$gender_terms   = get_the_terms( $page->ID, 'lez_actor_gender', true );
			$data['gender'] = ( $gender_terms && ! is_wp_error( $gender_terms ) ) ? join( ', ', wp_list_pluck( $gender_terms, 'name' ) ) : '';

			// Born
			if ( get_post_meta( $page->ID, 'lezactors_birth', true ) ) {
				$get_birth = new DateTime( get_post_meta( $page->ID, 'lezactors_birth', true ) );
			}

			// Died
			if ( get_post_meta( $page->ID, 'lezactors_death', true ) ) {
				$get_dead = new DateTime( get_post_meta( $page->ID, 'lezactors_death', true ) );
			}

			// Homepage
			$data['website'] = ( get_post_meta( $page->ID, 'lezactors_homepage', true ) ) ? esc_url( get_post_meta( $page->ID, 'lezactors_homepage', true ) ) : '';

			// Wikipedia
			$data['wikipedia'] = ( get_post_meta( $page->ID, 'lezactors_wikipedia', true ) ) ? esc_url( get_post_meta( $page->ID, 'lezactors_wikipedia', true ) ) : '';

			// IMdb
			$data['imdb'] = ( get_post_meta( $page->ID, 'lezactors_imdb', true ) ) ? 'https://imdb.com/name/' . get_post_meta( $page->ID, 'lezactors_imdb', true ) : '';

			// Twitter
			$data['twitter'] = ( get_post_meta( $page->ID, 'lezactors_twitter', true ) ) ? 'https://twitter.com/' . get_post_meta( $page->ID, 'lezactors_twitter', true ) : '';

			// Instagram
			$data['instagram'] = ( get_post_meta( $page->ID, 'lezactors_instagram', true ) ) ? 'https://instagram.com/' . get_post_meta( $page->ID, 'lezactors_instagram', true ) : '';

			$return = array(
				'uid'  => $page->ID,
				'id'   => $page->post_name,
				'name' => $page->post_title,
			);

			switch ( $format ) {
				case 'wiki':
					// Collect oddites
					$data['born'] = ( isset( $get_birth ) ) ? ' b. ' . date_format( $get_birth, 'F d, Y' ) : '';
					$data['died'] = ( isset( $get_dead ) ) ? ' and died ' . date_format( $get_dead, 'F d, Y' ) : '';

					// Now Build
					$return['description'] = $data['sexuality'] . ' ' . $data['gender'] . ' actor' . $data['born'] . $data['died'] . '. ' . $data['wikipedia'];
					break;
				case 'raw':
					$return['sexuality'] = $data['sexuality'];
					$return['gender']    = $data['gender'];
					$return['born']      = ( isset( $get_birth ) ) ? date_format( $get_birth, 'F d, Y' ) : '';
					$return['died']      = ( isset( $get_dead ) ) ? date_format( $get_dead, 'F d, Y' ) : '';
					$return['website']   = $data['website'];
					$return['imdb']      = $data['imdb'];
					$return['wikipedia'] = $data['wikipedia'];
					$return['twitter']   = $data['twitter'];
					$return['instagram'] = $data['instagram'];
					break;
			}
		}

		return $return;
	}

	/**
	 * Adds actions, filters, etc. to WP
	 *
	 * @access public
	 * @return void
	 * @since 1.1.0
	 */
	public function init() {
		// Plugin requires permalink usage - Only setup handling if permalinks enabled
		if ( '' !== get_option( 'permalink_structure' ) ) {

			// tell WP not to override query vars
			add_action( 'query_vars', array( $this, 'query_vars' ) );

			// add filter for pages
			add_action( 'template_redirect', array( $this, 'template_redirect' ) );

			$views = array( 'actor', 'character', 'show' );

			foreach ( $views as $a_view ) {
				add_rewrite_rule(
					'^' . $a_view . '/([^/]+)(?:/([0-9]+))?/wikidata/?$',
					'index.php?post_type_' . $a_view . 's&post_type=post_type_' . $a_view . 's&name=$matches[1]&export=wikidata',
					'top'
				);
				add_rewrite_rule(
					'^wikidata/' . $a_view . '/?$',
					'index.php?&exportname=' . $a_view . '&export=wikilist',
					'top'
				);
			}
		}
	}

	/**
	 * Add the query variables so WordPress won't override it
	 *
	 * @return $vars
	 * @since 1.1.0
	 */
	public function query_vars( $vars ) {
		$vars[] = 'export';
		$vars[] = 'exportname';
		return $vars;
	}

	/**
	 * Adds a custom template to the query queue.
	 *
	 * @return $templates
	 * @since 1.1.1
	 */
	public function template_redirect() {
		if ( get_query_var( 'export' ) ) {
			// phpcs:disable
			add_filter( 'template_include', function() {
				return dirname( __FILE__ ) . '/templates/export-json.php';
			});
			// phpcs:enable
		}
	}

}

new LWTV_Export_JSON();
