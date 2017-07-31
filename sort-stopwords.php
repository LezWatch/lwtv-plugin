<?php
/**
 * Filter post order by for shows
 *
 * We want to NOT include the/an/a when sorting.
 *
 * @since 1.2
 * Authors: Mika Epstein
 *
 */
if ( !is_front_page() ) {
	add_filter( 'posts_orderby', function( $orderby, \WP_Query $q ) {

		$reorder = false;
		$valid_tax = array( 'lez_country', 'lez_tropes', 'lez_genres', 'lez_formats', 'lez_stations' );
		$fwp_sort  = ( isset( $_GET['fwp_sort'] ) )? $_GET['fwp_sort'] : '';

	    if ( 'post_type_shows' !== $q->get( 'post_type' ) && 'date_desc' !== $fwp_sort ) return $orderby;

	    global $wpdb;

	    // Adjust this to your needs:
	    $matches = [ 'the ', 'an ', 'a ' ];

	    return sprintf(
	        " %s %s ",
	        lwtv_shows_posts_orderby_sql( $matches, " LOWER( {$wpdb->posts}.post_title) " ),
	        'ASC' === strtoupper( $q->get( 'order' ) ) ? 'ASC' : 'DESC'
	    );

	}, 10, 2 );

	function lwtv_shows_posts_orderby_sql( &$matches, $sql )
	{
	    if( empty( $matches ) || ! is_array( $matches ) )
	        return $sql;

	    $sql = sprintf( " TRIM( LEADING '%s' FROM ( %s ) ) ", $matches[0], $sql );
	    array_shift( $matches );
	    return lwtv_shows_posts_orderby_sql( $matches, $sql );
	}
}