<?php
/**
 * Name: Shows Like This
 * Description: Calculate other shows you'd like if you like this
 * This requires https://wordpress.org/plugins/related-posts-by-taxonomy/
 * See https://wordpress.org/support/topic/adding-meta-to-where-join-currently-it-replaces/
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Shows_Like_This
 *
 * @since 1.0
 */

class LWTV_Shows_Like_This {

	public function __construct() {
		add_filter( 'related_posts_by_taxonomy_posts_meta_query', array( $this, 'meta_query' ), 10, 4 );
	}

	public static function meta_query( $meta_query, $post_id, $taxonomies, $args ) {

		// $meta_query is an empty array if the format isn't thumbnails
		// if not empty it's the meta_query for the  _thumbnail_id meta key

		// Collect extras
		$worth = ( get_post_meta( $post_id, 'lezshows_worthit_rating', true ) ) ? get_post_meta( $post_id, 'lezshows_worthit_rating', true ) : 'TBD';
		$star  = ( get_post_meta( $post_id, 'lezshows_stars', true ) ) ? 'EXISTS' : 'NOT EXISTS';
		$score = ( get_post_meta( $post_id, 'lezshows_the_score', true ) ) ? get_post_meta( $post_id, 'lezshows_the_score', true ) : 10;

		// Stars: If there's ANY star, we would like another.
		$meta_query[] = array(
			'key'     => 'lezshows_stars',
			'compare' => $star,
		);

		// Worth: Thumb up/down/meh match
		$meta_query[] = array(
			'key'     => 'lezshows_worthit_rating',
			'value'   => $worth,
			'compare' => '=',
		);

		// Score: If the score is similar +/- 10
		$meta_query[] = array(
			'key'     => 'lezshows_the_score',
			'value'   => array( ( $score - 10 ), ( $score + 10 ) ),
			'type'    => 'numeric',
			'compare' => 'BETWEEN',
		);

		return $meta_query;
	}

}

new LWTV_Shows_Like_This();
