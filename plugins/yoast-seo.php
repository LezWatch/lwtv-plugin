<?php
/*
Description: Yoast SEO Customizations
Version: 1.2
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Yoast_SEO
 * Kill Yoast Stopwords
 *
 * Prevent stopwords from being removed from character and show pages
 * @since 1.0
 */
class LWTV_Yoast_SEO {

	/**
	 * Constructor
	 */
	public function __construct() {

		global $pagenow, $typenow;

		$pagenow_array = array( 'post.php', 'edit.php', 'post-new.php' );
		if ( !in_array( $pagenow , $pagenow_array ) ) return;

		// when editing pages, $typenow isn't set until later!
		if ( empty($typenow) ) {
			// try to pick it up from the query string
			if (!empty($_GET['post'])) {
				$post = get_post( sanitize_text_field($_GET['post']) );
				$typenow = $post->post_type;
			}
			// try to pick it up from the query string
			elseif ( !empty($_GET['post_type']) ) {
				$typenow = sanitize_text_field($_GET['post_type']);
			}
			// try to pick it up from the quick edit AJAX post
			elseif (!empty($_POST['post_ID'])) {
				$post = get_post( intval($_POST['post_ID']) );
				$typenow = $post->post_type;
			}
			else {
				$typenow = 'nopostfound';
			}
		}

		$typenow_array = array( 'post_type_shows', 'post_type_characters' );

		if ( !in_array( $typenow , $typenow_array ) ) return;

		add_filter( 'wpseo_stopwords', '__return_empty_array' );
		remove_action( 'get_sample_permalink', 'wpseo_remove_stopwords_sample_permalink', 10 );
	}
}

new LWTV_Yoast_SEO();