<?php
/**
 * Name: Calendar Names
 * Description: Sometimes we have weird names for the calendar.
 */

namespace LWTV\Calendar;

class Names {

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
	public function make( $name, $source ) {

		// Set Defaults:
		$check_name = array(
			'id'   => 0,
			'name' => $name,
		);

		// Check TV Maze
		$check_name = $this->tvmaze( $name );

		// If the ID is 0, try looking for a local show that matches.
		if ( 0 === $check_name['id'] ) {
			$check_name = $this->local( $name );
		}

		// Output depends on source calling.
		switch ( $source ) {
			case 'lwtv':
				// Return only the name
				return $check_name['name'];
			case 'tvmaze':
				if ( 0 === $check_name['id'] ) {
					return $check_name['name'];
				} else {
					return '<a href="' . get_permalink( $check_name['id'] ) . '">' . $check_name['name'] . '</a>';
				}
		}
	}

	/**
	 * Check TV Maze for a stored name.
	 *
	 * @param  string $name
	 * @return array
	 */
	private function tvmaze( string $name ): array {
		// Set base name and ID.
		$show_name = $name;
		$show_id   = 0;

		$tvmaze_obj = get_page_by_path( sanitize_title( $name ), OBJECT, 'post_type_tvmaze' );

		// If there is a TV Maze entry already, we use it.
		if ( isset( $tvmaze_obj->ID ) && 0 !== $tvmaze_obj->ID && 'publish' === get_post_status( $tvmaze_obj->ID ) ) {
			$post_id   = $tvmaze_obj->ID;
			$show      = get_post_meta( $post_id, 'leztvmaze_our_show' );
			$show_id   = $show[0][0];
			$show_name = get_the_title( $show_id );
		}

		return array(
			'id'   => $show_id,
			'name' => $show_name,
		);
	}

	/**
	 * Check local shows for the matching name.
	 *
	 * @param  string $name
	 * @return array
	 */
	private function local( string $name ): array {
		$show_name = $name;
		$show_id   = 0;

		// Find the show based on the LezWatch name
		$show_page_obj = get_page_by_path( sanitize_title( $name ), OBJECT, 'post_type_shows' );

		// If there is a local show with a full match, we can use it.
		if ( isset( $show_page_obj->ID ) && 0 !== $show_page_obj->ID && 'publish' === get_post_status( $show_page_obj->ID ) ) {
			$show_id   = $show_page_obj->ID;
			$show_name = get_the_title( $show_id );
		}

		return array(
			'id'   => $show_id,
			'name' => $show_name,
		);
	}
}
