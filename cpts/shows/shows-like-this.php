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
		add_filter( 'related_posts_by_taxonomy_cache', '__return_true' );
		add_filter( 'related_posts_by_taxonomy_wp_rest_api', '__return_true' );
	}

	public function generate( $show_id ) {
		$return = '';

		if ( ! empty( $show_id ) && has_filter( 'related_posts_by_taxonomy_posts_meta_query' ) ) {

			// Get the tags and add them to include if they exist. Default if so.
			$tagged  = get_the_terms( $show_id, 'lez_showtagged' );
			$exclude = '';
			$include = '';
			if ( ! empty( $tagged ) ) {
				foreach ( $tagged as $tag ) {
					$tags_array[] = $tag->term_id;
				}
				if ( ! isset( $tags_array ) || ! empty( $tags_array ) ) {
					$include    = implode( ', ', $tags_array );
					$taxonomies = 'lez_showtagged';
				}
			} else {
				// Not tagged? Terms!
				// Get the genre terms, we're going to include them
				$terms = get_the_terms( $show_id, 'lez_genres' );
				if ( isset( $terms ) && is_array( $terms ) ) {
					foreach ( $terms as $term ) {
						$terms_array[] = $term->term_id;
					}
				} else {
					$terms_array[] = '';
				}

				// Now. Get the primary
				$primary    = ( get_post_meta( $show_id, 'lezshows_tvgenre_primary', true ) ) ? get_post_meta( $show_id, 'lezshows_tvgenre_primary', true ) : false;
				$taxonomies = 'lez_genres';

				// If we have a primary, then we default to JUST that.
				if ( false !== $primary ) {
					$primary_key = array_search( $primary, $terms_array );
					if ( false !== $primary_key ) {
						unset( $terms_array[ $primary_key ] );
					}
					$exclude = implode( ', ', $terms_array );
					$include = $primary;
				} else {
					$include = implode( ', ', $terms_array );
				}
			}

			// Include the terms list
			$rpbt_include = 'include_terms=""' . $include . '" exclude_terms="' . $exclude . '"';

			$return = do_shortcode( '[related_posts_by_tax post_id="' . $show_id . '" fields="ids" order="RAND" title="" format="thumbnails" image_size="postloop-img" link_caption="true" posts_per_page="6" columns="0" post_class="similar-shows" taxonomies=" ' . $taxonomies . '" ' . $rpbt_include . ' related="false"]' );
		}

		if ( empty( $return ) ) {
			$return = false;
		}

		return $return;
	}

	public function meta_query( $meta_query, $post_id, $taxonomies, $args ) {

		if ( 'post_type_shows' === get_post_type( $post_id ) ) {
			/*
			 * The $meta_query variable is an array.
			 *
			 * If not empty it could be the meta query for post_thumbnails ( key '_thumbnail_id' )
			 * or some other meta query (from the shortcode or widget).
			 */
			$worthit = ( get_post_meta( $post_id, 'lezshows_worthit_rating', true ) ) ? get_post_meta( $post_id, 'lezshows_worthit_rating', true ) : false;

			// We should match up the worth-it value as well as the score.
			// After all, some low scores have a thumbs up.
			if ( false !== $worthit ) {
				$meta_query[] = array(
					'key'     => 'lezshows_worthit_rating',
					'compare' => $worthit,
				);
			}
		}

		return $meta_query;
	}

	/**
	 * Handpicked Reciprocity
	 *
	 * "Pick me, chose me, love me!" Meredith Grey wants McDreamy to love her. If another
	 * show has picked THIS show as a 'similar show', we want to pick it back.
	 *
	 * @param  int   $post_id Post ID of the show we're checking
	 * @return array          Array of posts that match
	 */
	public function reciprocity( $post_id ) {
		// If this isn't a show page, bail.
		if ( isset( $post_id ) && 'post_type_shows' !== get_post_type( $post_id ) ) {
			return;
		}

		$reciprocity      = array();
		$reciprocity_loop = new WP_Query(
			array(
				'post_type'              => 'post_type_shows',
				'post_status'            => array( 'publish' ),
				'orderby'                => 'title',
				'order'                  => 'ASC',
				'posts_per_page'         => '100',
				'no_found_rows'          => true,
				'update_post_term_cache' => true,
				'meta_query'             => array(
					array(
						'key'     => 'lezshows_similar_shows',
						'value'   => $post_id,
						'compare' => 'LIKE',
					),
				),
			)
		);

		if ( $reciprocity_loop->have_posts() ) {
			while ( $reciprocity_loop->have_posts() ) {
				$reciprocity_loop->the_post();
				$this_show_id = get_the_ID();
				$shows_array  = get_post_meta( $this_show_id, 'lezshows_similar_shows', true );

				/*
				 * If the show is published and there's a valid show array and it's not empty
				 * then we're going to loop through and see if the show ID matches exactly.
				 * If it does, we're going to save it to an array.
				 */
				if ( 'publish' === get_post_status( $this_show_id ) && isset( $shows_array ) && ! empty( $shows_array ) ) {
					foreach ( $shows_array as $related_show ) {
						// Because of show IDs having SIMILAR numbers, we need to be a little more flex
						// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
						if ( $related_show == $post_id ) {
							$reciprocity[] = $this_show_id;
						}
					}
				}
			}
			wp_reset_query();
			$reciprocity = wp_parse_id_list( $reciprocity );
		}

		return $reciprocity;
	}

	/**
	 * Alter Results
	 *
	 * Since we added in a CMB2 value for similar shows, we have to check that list here
	 * and make sure they're included.
	 *
	 * @param  array $results    The current results
	 * @param  int   $post_id    Post ID of the show
	 * @param  array $taxonomies All taxonomies,
	 * @param  array $args       Arguments passed through.
	 * @return array             The corrected results
	 */
	public function alter_results( $results, $post_id, $taxonomies, $args ) {

		if ( 'post_type_shows' === get_post_type( $post_id ) ) {
			// Set our base array
			$add_results = array();

			// The shortcode only allows post ids or post objects from the query.
			if ( ! empty( $results ) && empty( $args['fields'] ) ) {
				$results = wp_list_pluck( $results, 'ID' );
			}

			// What MIGHT we be adding:
			$handpicked  = ( get_post_meta( $post_id, 'lezshows_similar_shows', true ) ) ? wp_parse_id_list( get_post_meta( $post_id, 'lezshows_similar_shows', true ) ) : array();
			$reciprocity = self::reciprocity( $post_id );
			$combo_list  = array_merge( $handpicked, $reciprocity );

			// If we have a combo list, we need to figure out how many shows to add
			if ( ! empty( $combo_list ) ) {
				foreach ( $combo_list as $a_show ) {

					// Only go forward if the show is published
					// (you CAN add drafts, but they won't show up -- this helps us to pre-load)
					if ( 'publish' === get_post_status( $a_show ) ) {
						$add_results[] = $a_show;
					}
				}
			}

			// Make it unique
			$add_results = array_unique( $add_results );

			// Merge arrays, make them unique, and keep it to 6.
			$new_results = array_slice( array_unique( array_merge( $add_results, $results ) ), 0, 6 );
		}

		$return = ( isset( $new_results ) ) ? $new_results : $results;

		return $return;
	}
}

new LWTV_Shows_Like_This();
