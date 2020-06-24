<?php
/**
 * Name: Related Posts
 * Description: Display related shows/posts
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Related_Posts
 *
 * @since 2.1.0
 */

class LWTV_Related_Posts {

	public function __construct() {
		add_filter( 'the_content', array( $this, 'related_content' ) );
	}

	/**
	 * Output posts related to this show, character, or actor.
	 *
	 * @access public
	 * @param mixed $slug
	 * @return void
	 */
	public function related_posts( $slug ) {

		if ( ! self::are_there_posts( $slug ) ) {
			return;
		}

		$related_post_loop  = ( new LWTV_Loops() )->related_posts_by_tag( 'post', $slug );
		$related_post_query = wp_list_pluck( $related_post_loop->posts, 'ID' );
		$the_related_posts  = '<em>Coming soon...</em>';

		if ( $related_post_loop->have_posts() ) {

			// We get a max of 10 but we only want to show 5
			$max_posts  = 5;
			$count_post = 0;

			$the_related_posts = '<ul>';
			foreach ( $related_post_query as $related_post ) {
				if ( $count_post < '5' ) {
					$the_related_posts .= '<li><a href="' . get_the_permalink( $related_post ) . '">' . get_the_title( $related_post ) . '</a> &mdash; ' . get_the_date( get_option( 'date_format' ), $related_post ) . '</li>';
					$count_post++;
				}
			}
			$the_related_posts .= '</ul>';
		}

		return $the_related_posts;
	}

	/**
	 * Count the related posts
	 *
	 * @access public
	 * @static
	 * @param mixed $slug
	 * @return void
	 */
	public function count_related_posts( $slug ) {

		if ( ! self::are_there_posts( $slug ) ) {
			return;
		}

		$related_post_loop  = ( new LWTV_Loops() )->related_posts_by_tag( 'post', $slug );
		$related_post_query = wp_list_pluck( $related_post_loop->posts, 'ID' );

		return $related_post_query;
	}

	/**
	 * Are there related posts?
	 *
	 * @access public
	 * @static
	 * @param mixed $slug
	 * @return void
	 */
	public function are_there_posts( $slug ) {

		// If there are no posts with the tag, return false
		$term = term_exists( $slug, 'post_tag' );
		if ( 0 === $term || null === $term ) {
			return false;
		}

		// Elsa let it go and return true
		return true;
	}

	/**
	 * Related Content: Shows, Characters, or Actors related to this post
	 * Used on Posts only.
	 *
	 * @access public
	 * @param mixed $content
	 * @return void
	 */
	public function related_content( $content ) {
		if ( is_singular( 'post' ) ) {

			$post_tags   = get_the_tags( get_the_ID() );
			$related     = array(
				'show'      => '',
				'actor'     => '',
				'character' => '',
			);
			$icons       = array(
				'show'      => ( new LWTV_Functions() )->symbolicons( 'tv-hd.svg', 'fa-tv' ),
				'actor'     => ( new LWTV_Functions() )->symbolicons( 'team.svg', 'fa-users' ),
				'character' => ( new LWTV_Functions() )->symbolicons( 'contact-card.svg', 'fa-users' ),
			);
			$related_out = '';

			// Bail early if no tags or it's not an array.
			if ( $post_tags ) {
				foreach ( $post_tags as $tag ) {
					$maybe = array(
						'show'      => get_page_by_path( $tag->slug, OBJECT, 'post_type_shows' ),
						'actor'     => get_page_by_path( $tag->slug, OBJECT, 'post_type_actors' ),
						//'character' => get_page_by_path( $tag->slug, OBJECT, 'post_type_characters' ),
					);

					foreach ( $maybe as $type => $item ) {
						if ( $item && $item->post_name === $tag->slug ) {
							$related[ $type ] .= '<li>' . $icons[ $type ] . '<a href="/' . $type . '/' . $tag->slug . '">' . ucwords( $tag->name ) . '</a></li>';
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

new LWTV_Related_Posts();
