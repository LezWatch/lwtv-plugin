<?php
/**
 * namespace LWTV\Queeries;
 *
 * @since 5.0
 */

namespace LWTV\Queeries;

class WP_Meta {
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
	public function make( $key, $value, $compare = '=', $relation = 'AND' ) {

		global $wpdb;

		$query_args = array(
			'relation' => $relation,
			array(
				'key'     => $key,
				'value'   => $value,
				'compare' => $compare,
			),
		);
		$query      = new WP_Meta_Query( $query_args );

		$sql = $query->get_sql(
			'post',
			$wpdb->posts,
			'ID',
			null
		);

		return $sql;
	}
}
