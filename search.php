<?php
/*
 * Extra Search Functions for LezWatchTV.com
 *
 * @since 1.2
 * Authors: Mika Epstein
 */

/**
 * Search WordPress by Custom Fields
 *
 * https://adambalee.com/search-wordpress-by-custom-fields-without-a-plugin/
 * Extend WordPress search to include custom fields
 */

// Join posts and postmeta tables - http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
function lwtv_search_join( $join ) {
    global $wpdb;

    if ( is_search() ) {
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;
}

//Modify the search query with posts_where - http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
function lwtv_search_where( $where ) {
    global $pagenow, $wpdb;

    if ( is_search() ) {
		$keys = "'lezchars_actor', 'lezshows_worthit_details', 'lezshows_plots', 'lezshows_episodes', 'lezshows_realness_details', 'lezshows_quality_details', 'lezshows_screentime_details'";
		$where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR ( (".$wpdb->postmeta.".meta_key IN ( ".$keys." ) ) AND (".$wpdb->postmeta.".meta_value LIKE $1)  )", $where );
    }
    return $where;
}

// Prevent duplicates - http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
function lwtv_search_distinct( $where ) {
    global $wpdb;

    if ( is_search() ) {
        return "DISTINCT";
    }

    return $where;
}

// Only run if we're NOT in the admin screen!
if ( ! is_admin() ) {
	add_filter( 'posts_join', 'lwtv_search_join' );
	add_filter( 'posts_where', 'lwtv_search_where' );
	add_filter( 'posts_distinct', 'lwtv_search_distinct' );
}

/**
 * Pretty Permalinks for Search
 *
 * Forked from http://wordpress.org/extend/plugins/nice-search/
 */
function pretty_permalink_search_redirect() {
	// grab rewrite globals (tag prefixes, etc)
	// https://codex.wordpress.org/Class_Reference/WP_Rewrite
	global $wp_rewrite;

	// if we can't get rewrites or permalinks, we're probably not using pretty permalinks
	if ( !isset( $wp_rewrite ) || !is_object( $wp_rewrite ) || !$wp_rewrite->using_permalinks() )
		return;

	// Set Search Base - default is 'search'
	$search_base = $wp_rewrite->search_base;

	if ( is_search() && !is_admin() && strpos( $_SERVER['REQUEST_URI'], "/{$search_base}/" ) === false ) {

		// Get Post Types
		$query_post_types = get_query_var('post_type');
		if ( is_null($query_post_types) || empty($query_post_types) || !array($query_post_types) ) {
			$query_post_types = array( 'post_type_characters', 'post_type_shows' );
		}

		$query_post_type_url = '/?';
		foreach ( $query_post_types as $value ) {
			$query_post_type_url .= '&post_type[]=' . $value ;
		}

		wp_redirect(
			home_url( "/{$search_base}/"
			. urlencode( get_query_var( 's' ) )
			. urldecode( $query_post_type_url )
			) );
		exit();
	}
}
add_action( 'template_redirect', 'pretty_permalink_search_redirect' );