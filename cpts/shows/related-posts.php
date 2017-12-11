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

		if ( !self::are_there_posts( $slug ) ) return;

		$related_post_loop  = LWTV_Loops::related_posts_by_tag( 'post', $slug );
		$related_post_query = wp_list_pluck( $related_post_loop->posts, 'ID' );
		$the_related_posts  = '<em>Coming soon...</em>';

		if ( $related_post_loop->have_posts() ) {
			
			// We get a max of 10 but we only want to show 5
			$max_posts  = 5;
			$count_post = 0;
			
			$the_related_posts = '<ul>';
			foreach( $related_post_query as $related_post ) {
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
	public static function count_related_posts( $slug ) {

		if ( !self::are_there_posts( $slug ) ) return;

		$related_post_loop  = LWTV_Loops::related_posts_by_tag( 'post', $slug );
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
	public static function are_there_posts( $slug ) {
		
		// If there are no posts with the tag, return false
		$term = term_exists( $slug , 'post_tag' );
		if ( $term == 0 || $term == null ) return false;

		// Elsa let it go and return true
		return true;
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
						$shows .= '<li>' . lwtv_yikes_symbolicons( 'tv-hd.svg', 'fa-tv' ) . '<a href="/show/' . $tag->slug . '">'. ucwords( $tag->name ) . '</a></li>';
					}
				}

				if ( !empty( $shows ) ) {
					$related_shows = '<section class="related-shows"><div><h4 class="related-shows-title">Read more about the shows mentioned in this post:</h4><ul>' . $shows . '</ul></div></section>';
					$content .= $related_shows;
				}
			}
		}

		return $content;
	}
}

new LWTV_Related_Posts();