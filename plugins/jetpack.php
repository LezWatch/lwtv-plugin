<?php
/*
 * Jetpack tweaks
 * @version 1.1
 * @package lwtv-plugin
 */

class LWTV_Jetpack {

	public function __construct() {
		add_action( 'publish_post', array( $this, 'custom_message_save' ) );
	}

	public function publicize_hashtags() {
		$post = get_post();

		// If the post isn't empty AND it's a post (not a page etc), let's go!
		if ( ! empty( $post ) && 'post' === get_post_type( $post->ID ) ) {
			$post_tags = get_the_tags( $post->ID );
			if ( ! empty( $post_tags ) ) {
				// Create list of tags with hashtags in front of them
				$hash_tags = '';
				foreach ( $post_tags as $tag ) {
					// Limit this to shows only.
					$maybeshow = get_page_by_path( $tag->name, OBJECT, 'post_type_shows' );
					if ( $maybeshow->post_name === $tag->slug ) {
						// Change tag from this-name to thisname and slap a hashtag on it.
						$tag_name   = str_replace( '-', '', $tag->slug );
						$hash_tags .= ' #' . $tag_name;
					}
				}

				// Create our custom message
				$custom_message = 'New post! ' . get_the_title() . $hash_tags;
				update_post_meta( $post->ID, '_wpas_mess', $custom_message );
			}
		}
	}

	// Save that message
	public function custom_message_save() {
		add_action( 'save_post', array( $this, 'publicize_hashtags' ) );
	}

}

new LWTV_Jetpack();
