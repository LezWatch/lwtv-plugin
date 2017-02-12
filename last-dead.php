<?php
/**
 * LezWatch TV - The Dead
 *
 * The much vaunted "It has been X days since the last WLW Death"
 * This includes the code that is later used by a JSON API!
 *
 * To do: "On This Day..."
 *
 * Version:     1.0
 * Author:      Mika Epstein
 * Author URI:  https://halfelf.org
 * License: GPLv2.0 (or later)
 *
 */

// if this file is called directly abort
if ( ! defined('WPINC' ) ) {
	die;
}

/**
 * class LWTV_Dead_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_Dead_JSON {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init') );
	}

	/**
	 * Init
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init') );
	}

	/**
	 * Rest API init
	 *
	 * Creates the callback - /lwtv/v1/last-death/
	 */
	public function rest_api_init() {
		register_rest_route( 'lwtv/v1', '/last-death', array(
			'methods' => 'GET',
			'callback' => array( $this, 'last_death_rest_api_callback' ),
		) );
	}

	/**
	 * Rest API Callback
	 */
	public function last_death_rest_api_callback( $data ) {
		$response = $this->last_death();
		return $response;
	}

	/**
	 * Generate List of Dead
	 *
	 * @return array with last dead character data
	 */
	public static function last_death() {
		// Get all our dead queers
		$dead_chars_loop  = lwtvg_tax_query( 'post_type_characters' , 'lez_cliches', 'slug', 'dead');
		$dead_chars_query = wp_list_pluck( $dead_chars_loop->posts, 'ID' );

		// List all queers and the year they died
		if ( $dead_chars_loop->have_posts() ) {
			$death_list_array = array();

			// Loop through characters to build our list
			foreach( $dead_chars_query as $dead_char ) {

				// Date(s) character died
				$died_date = get_post_meta( $dead_char, 'lezchars_death_year', true);
				$died_date_array = array();

				// For each death date, create an item in an array with the unix timestamp
				foreach ( $died_date as $date ) {
					$date_parse = date_parse_from_format( 'm/d/Y' , $date);
					$died_date_array[] = mktime( $date_parse['hour'], $date_parse['minute'], $date_parse['second'], $date_parse['month'], $date_parse['day'], $date_parse['year'] );
				}

				// Grab the highest date (aka most recent)
				$died = max( $died_date_array );

				// Get the post slug
				$post_slug = get_post_field( 'post_name', get_post( $dead_char ) );

				// Add this character to the array
				$death_list_array[$post_slug] = array(
					'name' => get_the_title( $dead_char ),
					'url' => get_the_permalink( $dead_char ),
					'died' => $died,
				);
			}

			// Reorder all the dead to sort by DoD
			uasort($death_list_array, function($a, $b) {
				return $a['died'] <=> $b['died'];
			});
		}

		// Extract the last death
		$last_death = array_slice($death_list_array, -1, 1, true);
		$last_death = array_shift($last_death);

		// Calculate the difference between then and now
		$diff = abs( time() - $last_death['died'] );
		$last_death['since'] = $diff;

		$return = $last_death;

		return $return;
	}

}
new LWTV_Dead_JSON();