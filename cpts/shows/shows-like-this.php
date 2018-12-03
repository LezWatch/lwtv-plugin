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
		add_filter( 'related_posts_by_taxonomy', array( $this, 'alter_results' ), 10, 4 );
	}

	public static function generate( $show_id ) {
		$return = '';

		if ( ! empty( $show_id ) && has_filter( 'related_posts_by_taxonomy_posts_meta_query' ) ) {
			$return = do_shortcode( '[related_posts_by_tax post_id="' . $show_id . '" title="" format="thumbnails" image_size="postloop-img" link_caption="true" posts_per_page="6" columns="2" post_class="similar-shows" taxonomies="lez_formats,lez_tropes,lez_genres,lez_intersections"]' );
		}

		if ( empty( $return ) ) {
			$return = false;
		}

		return $return;
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

	public function alter_results( $results, $post_id, $taxonomies, $args ) {

		$handpicked  = ( get_post_meta( $post_id, 'lezshows_similar_shows', true ) ) ? get_post_meta( $post_id, 'lezshows_similar_shows', true ) : false;
		$add_results = array();

		if ( false !== $handpicked ) {
			foreach ( $handpicked as $a_show ) {
				// Omitting a LOT of things we don't need here
				// since we're not going to mess with output.
				$add_results[] = (object) array(
					'ID'              => $a_show,
					'post_author'     => get_post_field( 'post_author', $a_show ),
					'post_content'    => the_content( $a_show ),
					'post_title'      => get_the_title( $a_show ),
					'post_excerpt'    => get_the_excerpt( $a_show ),
					'post_status'     => get_post_status( $a_show ),
					'post_name'       => get_post_field( 'post_name', $a_show ),
					'rpbt_current'    => $post_id,
					'rpbt_post_class' => '',
					'rpbt_type'       => 'shortcode',
				);
			}
		}

		// Add our handpicked posts to the top of the list
		$results = $add_results + $results;

		// Give 'em back!
		return $results;
	}

}

new LWTV_Shows_Like_This();
