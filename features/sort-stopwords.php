<?php
/**
 * Filter post $orderby
 *
 * We want to NOT include the/an/a when sorting by changing the params
 * Massive props to Pascal Birchler for his cleverness with trim.
 *
 * @since 1.2
 *
 */

// Only run this on the front end.
if ( ! is_admin() ) {

	add_filter( 'posts_orderby', function( $orderby, \WP_Query $q ) {

		// Defaults:
		$resort         = false;
		$all_taxonomies = array( 'lez_stations', 'lez_tropes', 'lez_formats', 'lez_genres', 'lez_country', 'lez_stars', 'lez_triggers', 'lez_intersections' );
		$fwp_sort       = ( isset( $_GET['fwp_sort'] ) ) ? sanitize_text_field( $_GET['fwp_sort'] ) : 'empty'; // WPSC: CSRF ok.
		$fwp_array      = array( 'title_asc', 'title_desc', 'empty' );

		// Now to detect...
		// We only want to do this on archive pages
		if ( is_archive() ) {
			// If this is a show, we want to sort
			if ( 'post_type_shows' === $q->get( 'post_type' ) ) {
				$resort = true;
			}

			// If this is one of our listed taxonomies, we want to sort
			if ( in_array( $q->get( 'taxonomy' ), $all_taxonomies, true ) ) {
				$resort = true;
			}

			// If this is NOT in our valid arrays, we want to NOT sort.
			if ( ! in_array( $fwp_sort, $fwp_array, true ) ) {
				$resort = false;
			}
		}

		// If resort is false, we return orderby as is.
		if ( ! $resort ) {
			return $orderby;
		}

		// Okay! Time to go!
		global $wpdb;

		// Adjust this to your needs:
		$matches = [ 'a ', 'an ', 'lá ', 'la ', 'las ', 'les ', 'los ', 'el ', 'the ', '#' ];

		// Return our customized $orderby
		return sprintf(
			' %s %s ',
			lwtv_shows_posts_orderby_sql( $matches, " LOWER( {$wpdb->posts}.post_title) " ),
			'ASC' === strtoupper( $q->get( 'order' ) ) ? 'ASC' : 'DESC'
		);

	}, 10, 2 );


	/**
	 * lwtv_shows_posts_orderby_sql function.
	 *
	 * @access public
	 * @param mixed &$matches
	 * @param mixed $sql
	 * @return void
	 */
	function lwtv_shows_posts_orderby_sql( &$matches, $sql ) {
		if ( empty( $matches ) || ! is_array( $matches ) ) {
			return $sql;
		}

		$sql = sprintf( " TRIM( LEADING '%s' FROM ( %s ) ) ", $matches[0], $sql );
		array_shift( $matches );
		return lwtv_shows_posts_orderby_sql( $matches, $sql );
	}
}
