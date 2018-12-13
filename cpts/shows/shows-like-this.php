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
			$return = do_shortcode( '[related_posts_by_tax post_id="' . $show_id . '" title="" format="thumbnails" image_size="postloop-img" link_caption="true" posts_per_page="6" columns="0" post_class="similar-shows" taxonomies="lez_formats,lez_tropes,lez_genres,lez_intersections,lez_showtagged"]' );
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
		$star  = ( get_post_meta( $post_id, 'lezshows_stars', true ) ) ? 'EXISTS' : 'NOT EXISTS';
		$score = ( get_post_meta( $post_id, 'lezshows_the_score', true ) ) ? get_post_meta( $post_id, 'lezshows_the_score', true ) : 10;

		// Stars: If there's ANY star, we would like another.
		$meta_query[] = array(
			'key'     => 'lezshows_stars',
			'compare' => $star,
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
				$add_results[] = (object) get_post( $a_show, ARRAY_A );
			}
		}

		// Add our handpicked posts to the top of the list
		$combined = $add_results + $results;

		// Loop through to make sure Law & Order isn't listed x2
		$show_list = array();
		foreach ( $combined as $combined_show => $combined_data ) {
			$combined_data = (array) $combined_data;
			// Check if the show is already listed.
			if ( in_array( $combined_data['ID'], $show_list ) ) {
				// Remove from the combined results
				unset( $combined[ $combined_show ] );
			} else {
				// Add to the list for the next check
				$show_list[] = $combined_data['ID'];
			}
		}
		$results = $combined;

		// Give 'em back!
		return $results;
	}

}

new LWTV_Shows_Like_This();
