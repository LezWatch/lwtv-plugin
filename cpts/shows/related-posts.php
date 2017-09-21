<?php
/**
 * Name: Related Posts
 * Description: Show related shows/posts
 */

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Related_Posts
 *
 * @since 2.1.0
 */

class LWTV_Related_Posts {

	public function __construct() {
		add_filter( 'the_content', array( $this, 'related_shows' ) );
	}

	/**
	 * related_posts function.
	 *
	 * @access public
	 * @param mixed $slug
	 * @return void
	 */
	public static function related_posts( $slug ) {

		$term = term_exists( $slug , 'post_tag' );
		if ( $term == 0 || $term == null ) return;

		$related_post_loop  = LWTV_Loops::related_posts_by_tag( 'post', $slug );
		$related_post_query = wp_list_pluck( $related_post_loop->posts, 'ID' );
		$the_related_posts  = '<!-- No related posts published yet. -->';

		if ( $related_post_loop->have_posts() ) {
			$the_related_posts = '<h2>Related Posts</h2> <ul>';

			foreach( $related_post_query as $related_post ) {
				$the_related_posts .= '<li><a href="' . get_the_permalink( $related_post ) . '">' . get_the_title( $related_post ) . '</a> &mdash; ' . get_the_date( get_option( 'date_format' ), $related_post ) . '</li>';
			}

			$the_related_posts .= '</ul>';
		}

		return $the_related_posts;
	}

	/**
	 * related_shows function.
	 *
	 * @access public
	 * @param mixed $content
	 * @return void
	 */
	public function related_shows( $content ) {

	    if ( is_singular( 'post' ) ) {

			$posttags = get_the_tags( get_the_ID() );
			$shows = '';

			if ( $posttags ) {
				foreach( $posttags as $tag ) {
					if ( $post = get_page_by_path( $tag->name, OBJECT, 'post_type_shows' ) ) {
						$shows .= '<li><a href="/show/' . $tag->slug . '">'. ucwords( $tag->name ) . '</a></li>';
					}
				}

				if ( !is_null( $shows ) ) {
					$related_shows = '<section class="related-shows"><div><h4 class="related-shows-title">Read more about the shows mentioned in this post:</h4><ul>' . $shows . '</ul></div></section>';
					$content .= $related_shows;
				}
			}

	    }

	    return $content;
	}
}

new LWTV_Related_Posts();