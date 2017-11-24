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
if ( !is_admin() ) {
	
	add_filter( 'posts_orderby', function( $orderby, \WP_Query $q ) {
		
		// If this isn't an archive page, don't change $orderby
		if ( !is_archive() ) return $orderby;
		
		// If the post type isn't a show, don't change $orderby
		if ( 'post_type_shows' !== $q->get( 'post_type' ) ) return $orderby;

		// If the sort isn't based on title, don't change $orderby
		$fwp_sort  = ( isset( $_GET['fwp_sort'] ) )? sanitize_text_field( $_GET['fwp_sort'] ) : 'empty';
		$fwp_array = array( 'title_asc', 'title_desc', 'empty');
		if ( !in_array( $fwp_sort, $fwp_array ) ) return $orderby;

		// Okay! Time to go!
		global $wpdb;

		// Adjust this to your needs:
		$matches = [ 'the ', 'an ', 'a ', 'la ', 'el ', 'las ', 'les ', 'los ' ];

		// Return our customized $orderby
		return sprintf(
			" %s %s ",
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
		if( empty( $matches ) || ! is_array( $matches ) )
			return $sql;

		$sql = sprintf( " TRIM( LEADING '%s' FROM ( %s ) ) ", $matches[0], $sql );
		array_shift( $matches );
		return lwtv_shows_posts_orderby_sql( $matches, $sql );
	}
}