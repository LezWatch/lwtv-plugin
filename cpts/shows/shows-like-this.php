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
			$return = do_shortcode( '[related_posts_by_tax post_id="' . $show_id . '" fields="ids" order="RAND" title="" format="thumbnails" image_size="postloop-img" link_caption="true" posts_per_page="6" columns="0" post_class="similar-shows" taxonomies="lez_tropes,lez_genres,lez_intersections,lez_showtagged"]' );
		}

		if ( empty( $return ) ) {
			$return = false;
		}

		return $return;
	}

	public static function meta_query( $meta_query, $post_id, $taxonomies, $args ) {

		/*
		 * The $meta_query variable is an array.
		 *
		 * If not empty it could be the meta query for post_thumbnails ( key '_thumbnail_id' )
		 * or some other meta query (from the shortcode or widget).
		 */

		// Collect extras
		$star  = ( get_post_meta( $post_id, 'lezshows_stars', true ) ) ? 'EXISTS' : 'NOT EXISTS';
		$loved = ( get_post_meta( $post_id, 'lezshows_worthit_show_we_love', true ) ) ? true : false;
		$score = ( get_post_meta( $post_id, 'lezshows_the_score', true ) ) ? get_post_meta( $post_id, 'lezshows_the_score', true ) : 10;

		// Stars: If there's ANY star, we would like another.
		$meta_query[] = array(
			'key'     => 'lezshows_stars',
			'compare' => $star,
		);

		// If the show is loved, we want to include it here.
		if ( $loved ) {
			$meta_query[] = array(
				'key'     => 'lezshows_worthit_show_we_love',
				'compare' => 'EXISTS',
			);
		}

		// If they're NOT loved, we use the scores for a value.
		if ( ! $loved ) {
			// Score: If the score is similar +/- 10
			if ( $score >= 90 ) {
				$score_range = array( 75, 100 );
			} elseif ( $score <= 10 ) {
				$score_range = array( 10, 30 );
			} else {
				$score_range = array( ( $score - 10 ), ( $score + 10 ) );
			}
			$meta_query[] = array(
				'key'     => 'lezshows_the_score',
				'value'   => $score_range,
				'type'    => 'numeric',
				'compare' => 'BETWEEN',
			);
		}

		return $meta_query;
	}

	public function alter_results( $results, $post_id, $taxonomies, $args ) {

		$handpicked  = ( get_post_meta( $post_id, 'lezshows_similar_shows', true ) ) ? get_post_meta( $post_id, 'lezshows_similar_shows', true ) : false;
		$add_results = array();

		// The shortcode only allows post ids or post objects from the query.
		if ( ! empty( $results ) && empty( $args['fields'] ) ) {
			$results = wp_list_pluck( $results, 'ID' );
		}

		if ( false !== $handpicked ) {
			// Array with ids.
			$handpicked = wp_parse_id_list( $handpicked );

			// For each show, add it to the list ONLY if the show isn't already listed.
			foreach ( $handpicked as $a_show ) {
				//phpcs:ignore WordPress.PHP.StrictInArray
				if ( ! in_array( $a_show, $results ) ) {
					$add_results[] = $a_show;
				}
			}
		}

		// Add our handpicked posts to the list
		$results = $add_results + $results;

		// Give 'em back!
		return $results;
	}
}

new LWTV_Shows_Like_This();
