<?php
/**
 * Name: Calendar Names
 * Description: Sometimes we have weird names for the calendar.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Calendar_Names {

	// @TODO: Delete this once the new code is imported.
	const SHOW_NAMES = array(
		// TV MAZE NAME                  => OUR NAME
		'Mythic Quest: Raven\'s Banquet' => 'Mythic Quest',
	);

	/**
	 * Check Show Name
	 *
	 * Since TV Maze sometimes uses different names than we do, we have to make
	 * a related array that can handle two names.
	 *
	 * @TODO: Make this something we have UX access to.
	 *
	 * @param  string $showname Display Name of the show
	 * @param  string $source   lwtv or tvmaze
	 * @return string           The display name
	 */
	public function check_name( $name, $source ) {

		// Save the original
		$display_name = $name;

		// Check TV Maze first:
		$tvmaze_obj = get_page_by_path( sanitize_title( $name ), OBJECT, 'post_type_tvmaze' );

		// If there is a TV Maze entry already, we use it.
		if ( isset( $tvmaze_obj->ID ) && 0 !== $tvmaze_obj->ID && 'publish' === get_post_status( $tvmaze_obj->ID ) ) {
			$post_id   = $tvmaze_obj->ID;
			$show      = get_post_meta( $post_id, 'leztvmaze_our_show' );
			$show_id   = $show[0][0];
			$show_name = get_the_title( $show_id );
		}

		// if there's no show ID, we check the default array
		// This will be deleted later when we're done with that array.
		if ( ! isset( $show_id ) ) {
			$name_array = self::SHOW_NAMES;

			$search_array = $name_array;
			if ( 'lwtv' === $source ) {
				// this will be faster.
				$search_array = array_flip( $name_array );
			}

			if ( isset( $search_array[ $display_name ] ) ) {
				$display_name = $search_array[ $display_name ];
			}

			// Find the show based on the LezWatch name
			$show_page_obj = get_page_by_path( sanitize_title( $name ), OBJECT, 'post_type_shows' );

			// If we have a show, we will link.
			if ( isset( $show_page_obj->ID ) && 0 !== $show_page_obj->ID && 'publish' === get_post_status( $show_page_obj->ID ) ) {
				$show_id   = $show_page_obj->ID;
				$show_name = $show_page_obj->post_title;
			}
		}

		switch ( $source ) {
			case 'lwtv':
				return $show_name;
			case 'tvmaze':
				return '<a href="' . get_permalink( $show_id ) . '">' . $show_name . '</a>';
		}
	}
}
