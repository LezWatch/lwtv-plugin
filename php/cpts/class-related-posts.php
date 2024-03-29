<?php
/**
 * Name: Related Posts
 * Description: Display related shows/posts
 */

namespace LWTV\CPTs;

class Related_Posts {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'the_content', array( $this, 'related_content' ) );
	}

	/**
	 * Output posts related to this show, character, or actor.
	 *
	 * @param  string $slug
	 * @return string
	 */
	public function related_posts( $slug ): mixed {

		// Default content:
		$the_related_posts = '<em>Coming soon...</em>';

		// If there are no posts, return early.
		if ( ! self::are_there_posts( $slug ) ) {
			return null;
		}

		$related_post_loop = lwtv_plugin()->get_related_posts_by_tag( 'post', $slug );
		if ( is_object( $related_post_loop ) && $related_post_loop->have_posts() ) {
			$related_post_query = array_unique( wp_list_pluck( $related_post_loop->posts, 'ID' ) );
		}

		if ( isset( $related_post_query ) && is_array( $related_post_query ) ) {
			// We get a max of 10 but we only want to show 3
			$max_posts  = 3;
			$count_post = 0;

			$the_related_posts = '<div class="container related-post-container"><div class="row site-loop related-post-loop">';
			foreach ( $related_post_query as $related_post ) {
				if ( $count_post < $max_posts ) {
					$the_related_posts .= '<div class="card">
					<center>' . get_the_post_thumbnail( $related_post, 'medium' ) . '</center>
					<div class="card-body">
						<h5 class="card-title"><a href="' . get_the_permalink( $related_post ) . '">' . get_the_title( $related_post ) . '</a></h5>
						<p><em><small>' . get_the_date( get_option( 'date_format' ), $related_post ) . '</small></em></p>
					</div>
					</div>';
					++$count_post;
				}
			}
			$the_related_posts .= '</div></div>';

			if ( count( $this->count_related_posts( $slug ) ) > $max_posts ) {
				$get_tags = term_exists( $slug, 'post_tag' );
				if ( ! is_null( $get_tags ) && $get_tags >= 1 ) {
					$the_related_posts .= '<p class="read-more"><a href="' . esc_url( get_tag_link( $get_tags['term_id'] ) ) . '" class="btn btn-outline-primary">Read More ...</a></p>';
				}
			}
		}

		return $the_related_posts;
	}

	/**
	 * Count the related posts
	 *
	 * @param string $slug
	 * @return array
	 */
	public function count_related_posts( $slug ): array {

		// If there are no posts, return empty.
		if ( ! self::are_there_posts( $slug ) ) {
			return array();
		}

		$related_post_loop = lwtv_plugin()->get_related_posts_by_tag( 'post', $slug );

		// If this isn't an object, return empty.
		if ( ! is_object( $related_post_loop ) ) {
			return array();
		}

		$related_post_query = wp_list_pluck( $related_post_loop->posts, 'ID' );
		$related_post_query = array_unique( $related_post_query );

		return $related_post_query;
	}

	/**
	 * Are there related posts?
	 *
	 * @param mixed $slug
	 * @return bool
	 */
	public function are_there_posts( $slug ): bool {

		// If there are no posts with the tag, return false.
		$term = term_exists( $slug, 'post_tag' );
		if ( 0 === $term || null === $term ) {
			return false;
		}

		// If there are no posts IN the tag, return false.
		$term_data = get_term_by( 'id', $term['term_id'], 'post_tag' );
		if ( ! isset( $term_data->count ) ) {
			return false;
		}

		// Elsa let it go and return true.
		return true;
	}

	/**
	 * Related Content Archive
	 *
	 * @param int     $tag_id ID of tag for the archive.
	 *
	 * @return string The related items.
	 */
	public function related_archive_header( $tag_id ): string {
		$related     = '';
		$tag         = get_tag( $tag_id );
		$linked_post = get_term_meta( $tag->term_id, 'lez_termsmeta_linked_post', true );
		$icons       = array(
			'show'      => lwtv_plugin()->get_symbolicon( 'tv-hd.svg', 'fa-tv' ),
			'actor'     => lwtv_plugin()->get_symbolicon( 'team.svg', 'fa-users' ),
			'character' => lwtv_plugin()->get_symbolicon( 'contact-card.svg', 'fa-users' ),
		);

		if ( ! empty( $linked_post ) ) {
			// If we have a linked post, we will trust it.
			$type    = rtrim( str_replace( 'post_type_', '', get_post_type( $linked_post ) ), 's' );
			$related = '<p><a href="' . get_permalink( $linked_post ) . '">' . get_the_title( $linked_post ) . '</a></p>';
		} else {
			// There's no post linked, so we're going to do it the hard way:
			$maybe         = array(
				'show'  => get_page_by_path( $tag->slug, OBJECT, 'post_type_shows' ),
				'actor' => get_page_by_path( $tag->slug, OBJECT, 'post_type_actors' ),
			);
			$related_items = '';
			foreach ( $maybe as $type => $item ) {
				if ( $item && $item->post_name === $tag->slug ) {
					$related_items = '<a class="btn btn-outline-primary btn-lg" href="/' . $type . '/' . $tag->slug . '">' . $icons[ $type ] . ' Learn more about this ' . $type . ' </a></br>';
					break;
				}
			}

			if ( ! empty( $related_items ) ) {
				$related = '<p>' . $related_items . '</p>';
			}
		}

		return $related;
	}

	/**
	 * Related Content: Shows, Characters, or Actors related to this post
	 * Used on Posts only.
	 *
	 * @param  string $content
	 * @return string Update content
	 */
	public function related_content( $content ): string {
		if ( is_singular( 'post' ) ) {

			$post_tags   = get_the_tags( get_the_ID() );
			$related     = array(
				'show'      => '',
				'actor'     => '',
				'character' => '',
			);
			$icons       = array(
				'show'      => lwtv_plugin()->get_symbolicon( 'tv-hd.svg', 'fa-tv' ),
				'actor'     => lwtv_plugin()->get_symbolicon( 'team.svg', 'fa-users' ),
				'character' => lwtv_plugin()->get_symbolicon( 'contact-card.svg', 'fa-users' ),
			);
			$related_out = '';

			// Bail early if no tags or it's not an array.
			if ( $post_tags ) {
				foreach ( $post_tags as $tag ) {

					$linked_post = get_term_meta( $tag->term_id, 'lez_termsmeta_linked_post', true );

					if ( ! empty( $linked_post ) ) {
						// If we have a linked post, we will trust it.
						$type              = rtrim( str_replace( 'post_type_', '', get_post_type( $linked_post ) ), 's' );
						$related[ $type ] .= '<li>' . $icons[ $type ] . '<a href="' . get_permalink( $linked_post ) . '">' . get_the_title( $linked_post ) . '</a></li>';
					} else {
						// There's no post linked, so we're going to do it the hard way:
						$maybe = array(
							'show'  => get_page_by_path( $tag->slug, OBJECT, 'post_type_shows' ),
							'actor' => get_page_by_path( $tag->slug, OBJECT, 'post_type_actors' ),
						);
						foreach ( $maybe as $type => $item ) {
							if ( $item && $item->post_name === $tag->slug ) {
								$related[ $type ] .= '<li>' . $icons[ $type ] . '<a href="/' . $type . '/' . $tag->slug . '">' . ucwords( $tag->name ) . '</a></li>';
							}
						}
					}
				}

				foreach ( $related as $type => $item ) {
					if ( ! empty( $related[ $type ] ) ) {
						$related_out .= $item;
					}
				}

				if ( ! empty( $related_out ) ) {
					$related_content = '<section class="related-shows"><div><h4 class="related-shows-title">Read more about topics mentioned in this post:</h4><ul class="related-shows-list">' . $related_out . '</ul></div></section>';
					$content        .= $related_content;
				}
			}
		}

		return $content;
	}
}
