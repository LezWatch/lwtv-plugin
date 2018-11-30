<?php
/**
 * Name: Shows Like This
 * Description: Calculate other shows you'd like if you like this
 * This requires https://wordpress.org/plugins/related-posts-by-taxonomy/
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
		add_filter( 'related_posts_by_taxonomy_pre_related_posts', array( $this, 'override' ), 10, 4 );
	}

	public static function override( $related_posts, $args ) {
		// Use widget or shortcode settings for our own query.
		$my_query_args = array(
			'post_type'      => $args['post_types'],
			'posts_per_page' => $args['posts_per_page'],
			'public_only'    => $args['public_only'],
			'include_self'   => $args['include_self'],
		);

		// Collect extras
		$thumb = ( get_post_meta( $args['post_id'], 'lezshows_worthit_rating', true ) ) ? get_post_meta( $args['post_id'], 'lezshows_worthit_rating', true ) : 'TBD';
		$star  = ( get_post_meta( $args['post_id'], 'lezshows_stars', true ) ) ? 'EXISTS' : 'NOT EXISTS';
		$score = ( get_post_meta( $args['post_id'], 'lezshows_the_score', true ) ) ? get_post_meta( $args['post_id'], 'lezshows_the_score', true ) : 10;

		$meta_array = array(
			// If it has ANY star
			array(
				'key'     => 'lezshows_stars',
				'compare' => $star,
			),
			// If it's worth it
			array(
				'key'     => 'lezshows_worthit_rating',
				'value'   => $thumb,
				'compare' => '=',
			),
			// If the score is similar +/- 10
			array(
				'key'     => 'lezshows_the_score',
				'value'   => array( ( $score - 10 ), ( $score + 10 ) ),
				'type'    => 'numeric',
				'compare' => 'BETWEEN',
			),
		);

		$my_query_args['meta_query'] = $meta_array;

		$my_related_posts = get_posts( $my_query_args );

		return $my_related_posts;
	}

}

new LWTV_Shows_Like_This();
