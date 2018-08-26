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

	/**
	 * Add tags to tweets etc as hashtags, if they're also shows
	 * @return void
	 */
	public function publicize_hashtags() {
		$post             = get_post();
		$previous_message = get_post_meta( $post->ID, '_wpas_mess', true );
		$hash_tags        = '';

		// If the post isn't empty AND it's a post (not a page etc), let's go!
		if ( ! empty( $post ) && 'post' === get_post_type( $post->ID ) ) {
			$post_tags = get_the_tags( $post->ID );
			if ( ! empty( $post_tags ) ) {
				// Create list of tags with hashtags in front of them
				foreach ( $post_tags as $tag ) {
					// Limit this to shows only.
					$maybeshow = get_page_by_path( $tag->name, OBJECT, 'post_type_shows' );
					if ( $maybeshow->post_name === $tag->slug ) {
						// Change tag from this-name to This-Name
						$tag_caps = ucwords( $tag->slug, '-' );
						// Change This-Name to this-name
						$tag_name   = str_replace( '-', '', $tag_caps );
						$hash_tags .= ' #' . $tag_name;
					}
				}
			}
		}

		// Loop back. If there are hashtags, we add them.
		if ( '' !== $hash_tags ) {
			// Create our custom message. If there's one already set, we will
			// use that. Else, we use the title.
			$custom_message  = ( ! empty( $previous_message ) ) ? $previous_message : get_the_title();
			$custom_message .= $hash_tags;
			update_post_meta( $post->ID, '_wpas_mess', $custom_message );
		}
	}

	/**
	 * Save the custom message
	 * @return void
	 */
	public function custom_message_save() {
		add_action( 'save_post', array( $this, 'publicize_hashtags' ), 21 );
	}
}

new LWTV_Jetpack();
