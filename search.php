<?php
/*
 * Extra Search Functions for LezWatch.TV.com
 *
 * @since 1.2
 */

/**
 * class LWTV_Search
 */
class LWTV_Search {

	public function __construct(){
		// Only run if we're NOT in the admin screen!
		if ( ! is_admin() ) {
			add_filter( 'posts_join',  array( $this, 'search_join' ) );
			add_filter( 'posts_where',  array( $this, 'search_where' ) );
			add_filter( 'posts_distinct',  array( $this, 'search_distinct' ) );
		}
		add_action( 'template_redirect', array( $this, 'search_redirect' ) );
	}

	/**
	 * Join posts and postmeta tables
	 *  - http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
	 *
	 * @param string $join Search string
	 * @return string $join Modified string with post and postmeta based on post ID
	 *
	 * @since 1.0
	 */
	public function search_join( $join ) {
		global $wpdb;

		if ( is_search() ) {
			$join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
		}

		return $join;
	}

	/**
	 * Modify Search Location
	 *  - http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
	 *
	 * Extend WordPress search to include custom fields
	 * https://adambalee.com/search-wordpress-by-custom-fields-without-a-plugin/
	 *
	 * @param string $where
	 * @return string $where
	 *
	 * @since 1.0
	 */
	public function search_where( $where ) {
		global $pagenow, $wpdb;

		if ( is_search() ) {
			$keys = "'lezchars_actor', 'lezshows_worthit_details', 'lezshows_plots', 'lezshows_episodes', 'lezshows_realness_details', 'lezshows_quality_details', 'lezshows_screentime_details'";
			$where = preg_replace(
			"/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
			"(".$wpdb->posts.".post_title LIKE $1) OR ( (".$wpdb->postmeta.".meta_key IN ( ".$keys." ) ) AND (".$wpdb->postmeta.".meta_value LIKE $1)  )", $where );
		}
		return $where;
	}

	/**
	 * Distinct Queries Only
	 *  - http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
	 *
	 * Force search to be distinct and prevent duplicates
	 *
	 * @param string $where
	 * @return string $where
	 *
	 * @since 1.0
	 */
	public function search_distinct( $where ) {
		global $wpdb;

		if ( is_search() ) {
			return "DISTINCT";
		}
		return $where;
	}

	/**
	 * Pretty Permalinks for Search
	 *
	 * Forked from http://wordpress.org/extend/plugins/nice-search/
	 *
	 * @since 2.0
	 */
	public function search_redirect() {
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
			$query_post_types = get_query_var( 'post_type' );
			
			if ( is_null( $query_post_types ) || empty( $query_post_types ) || !array( $query_post_types ) ) {
				$query_post_types = array( 'post_type_characters', 'post_type_shows', 'post_type_actors' );
			}

			if ( $query_post_types == 'any' ) $query_post_types = array( 'post', 'page', 'post_type_characters', 'post_type_shows', 'post_type_actors' );

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
}

new LWTV_Search();