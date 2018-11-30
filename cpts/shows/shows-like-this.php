<?php
/**
 * Name: Shows Like This
 * Description: Calculate other shows you'd like if you like this
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
	}

	/**
	 * Output posts related to this show, character, or actor.
	 *
	 * @access public
	 * @param mixed $slug
	 * @return void
	 */
	public static function similar_shows( $show_id ) {
		$count = wp_count_posts( 'post_type_shows' )->publish;
		$thumb = ( get_post_meta( $show_id, 'lezshows_worthit_rating', true ) ) ? get_post_meta( $show_id, 'lezshows_worthit_rating', true ) : 'TBD';
		$star  = ( get_post_meta( $show_id, 'lezshows_stars', true ) ) ? 'EXISTS' : 'NOT EXISTS';
		$score = ( get_post_meta( $show_id, 'lezshows_the_score', true ) ) ? get_post_meta( $show_id, 'lezshows_the_score', true ) : 10;

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

		$queery = new WP_Query(
			array(
				'post_type'              => 'post_type_shows',
				'posts_per_page'         => $count,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'post_status'            => array( 'publish' ),
				'meta_query'             => $meta_array,
			)
		);
		wp_reset_query();

	}

}

new LWTV_Shows_Like_This();
