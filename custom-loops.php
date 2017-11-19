<?php
/**
 * Name: Custom Loops
 * Description: Custom arrays and WP_Query calls that are repeated
 */

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Loops
 *
 * Customize Loops
 *
 * @since 1.0
 */

class LWTV_Loops {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Nothing to see here
	}

	/*
	 * Taxonomy Array
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $taxonomy The taxonomy being searched
	 * @param array $term The term being searched for.
	 * @param string $operator Search operator. Default IN.
	 *
	 * @return array The WP_Query Array
	 */
	public static function tax_query( $post_type, $taxonomy, $field, $term, $operator = 'IN' ) {
		$count = wp_count_posts( $post_type )->publish;
		$query = new WP_Query ( array(
			'post_type'              => $post_type,
			'posts_per_page'         => $count,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'post_status'            => array( 'publish' ),
			'tax_query'              => array( array(
				'taxonomy' => $taxonomy,
				'field'    => $field,
				'terms'    => $term,
				'operator' => $operator,
			),),
		) );
		wp_reset_query();
		return $query;
	}
	/*
	 * Taxonomy Two Array
	 *
	 * This check is used for generating a query of posts that are in two taxonomies.
	 * For example you can use it to loop through dead queers who are non-binary
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $taxonomy1 The taxonomy being searched
	 * @param array $term1 The term being searched for.
	 * @param string $taxonomy2 The taxonomy being searched
	 * @param array $term2 The term being searched for.
	 * @param string $operator1 Search operator. Default IN.
	 * @param string $operator2 Search operator. Default IN.
	 *
	 * @return array The WP_Query Array
	 */
	public static function tax_two_query( $post_type, $taxonomy1, $field1, $terms1, $taxonomy2, $field2, $terms2, $operator1 = 'IN', $operator2 = 'IN' , $relation = 'AND' ) {
		$count = wp_count_posts( $post_type )->publish;
		$query = new WP_Query ( array(
			'post_type'              => $post_type,
			'posts_per_page'         => $count,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'post_status'            => array( 'publish' ),
			'relation'               => $relation,
			'tax_query'              => array(
				array(
					'taxonomy' => $taxonomy1,
					'field'    => $field1,
					'terms'    => $terms1,
					'operator' => $operator1,
				),
				array(
					'taxonomy' => $taxonomy2,
					'field'    => $field2,
					'terms'    => $terms2,
					'operator' => $operator2,
				),
			),
		));
		wp_reset_query();
		return $query;
	}

	/*
	 * Post Meta Array
	 *
	 * For when you need the whole post data
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $key The post meta key being searched for.
	 * @param string $value The post meta VALUE being searched for.
	 * @param string $compare Search operator. Default =
	 *
	 * @return array The WP_Query Array
	 */
	public static function post_meta_query( $post_type, $key, $value, $compare = '=' ) {
		$count = wp_count_posts( $post_type )->publish;
		if ( $value != '' ) {
			$query = new WP_Query( array(
				'post_type'              => $post_type,
				'post_status'            => array( 'publish' ),
				'orderby'                => 'title',
				'order'                  => 'ASC',
				'posts_per_page'         => $count,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'meta_query'             => array( array(
					'key'     => $key,
					'value'   => $value,
					'compare' => $compare,
				),),
			) );
		} else {
			$query = new WP_Query( array(
				'post_type'              => $post_type,
				'post_status'            => array( 'publish' ),
				'orderby'                => 'title',
				'order'                  => 'ASC',
				'posts_per_page'         => $count,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'meta_query'             => array( array(
					'key'     => $key,
					'compare' => $compare,
				),),
			) );
		}

		wp_reset_query();
		return $query;
	}

	/*
	 * WP Meta Query
	 *
	 * For when you need the whole post data
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $key The post meta key being searched for.
	 * @param string $value The post meta VALUE being searched for.
	 * @param string $compare Search operator. Default =
	 *
	 * @return array The WP_Query Array
	 */
	public static function wp_meta_query( $key, $value, $compare = '=', $relation = 'AND' ) {

		global $wpdb;

		$query_args = array(
			'relation' => $relation,
			array(
				'key'     => $key,
				'value'   => $value,
				'compare' => $compare,
			)
		);
		$query = new WP_Meta_Query( $query_args );

		$sql = $query->get_sql(
			'post',
			$wpdb->posts,
			'ID',
			null
		);

		return $sql;
	}

	/*
	 * Post Type Array
	 *
	 * Generate an array of all posts in a specific post type.
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 *
	 * @return array The WP_Query Array
	 */

	public static function post_type_query( $post_type ) {
		$count = wp_count_posts( $post_type )->publish;
		$query = new WP_Query ( array(
				'post_type'              => $post_type,
				'posts_per_page'         => $count,
				'orderby'                => 'title',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'post_status'            => array( 'publish' ),
			)
		);
		wp_reset_query();
		return $query;
	}

	/*
	 * Post Meta AND Taxonomy Query
	 *
	 * Function to generate an array of posts that have a specific post meta AND
	 * a specific taxonomy value. Useful for getting a list of all dead queers
	 * who are main characters (for example).
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $key The post meta key being searched for.
	 * @param string $value The post meta VALUE being searched for.
	 * @param string $taxonomy The taxonomy being searched
	 * @param array $term The term being searched for.
	 * @param string $compare Search operator for meta_query. Default =
	 * @param string $operator Search operator for tax_query. Default IN.
	 *
	 * @return array The WP_Query Array
	 */

	public static function post_meta_and_tax_query( $post_type, $key, $value, $taxonomy, $field, $terms, $compare = '=', $operator = 'IN' ) {
		$count = wp_count_posts( $post_type )->publish;
		$query = new WP_Query( array(
			'post_type'       => $post_type,
			'posts_per_page'  => $count,
			'no_found_rows'   => true,
			'post_status'     => array( 'publish' ),
			'meta_query'      => array(
				array(
					'key'     => $key,
					'value'   => $value,
					'compare' => $compare,
				),
			),
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => $field,
					'terms'    => $terms,
					'operator' => $operator,
				),
			),
		) );

		wp_reset_query();
		return $query;
	}

	/**
	 * Related Posts by Tags.
	 *
	 * @access public
	 * @static
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $slug i.e. the slug of the post we're trying to relate to
	 * @return void
	 */
	public static function related_posts_by_tag( $post_type, $slug ) {
		$term = term_exists( $slug, 'post_tag' );
		if ( $term == 0 || $term == null ) return;

		$query = new WP_Query( array(
			'post_type'       => $post_type,
			'no_found_rows'   => true,
			'post_status'     => array( 'publish' ),
			'tag'             => $slug,
			'orderby'         => 'date',
			'order'           => 'DESC',
		) );

		wp_reset_query();
		return $query;
	}

}

new LWTV_Loops();