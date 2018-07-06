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
		if ( ! empty( $post ) ) {
			if ( is_singular( 'post' ) ) {
				$post_tags = get_the_tags( $post->ID );

				// Append tags to custom message
				if ( ! empty( $post_tags ) ) {

					// Create list of tags with hashtags in front of them
					$hash_tags = '';
					foreach ( $post_tags as $tag ) {
						// Limit this to shows only.
						$this_post = get_page_by_path( $tag->name, OBJECT, 'post_type_shows' );
						if ( $post === $this_post ) {
							// Change tag from this-name to thisname
							$tag_name   = str_replace( '-', '', $tag );
							$hash_tags .= ' #' . $tag->name;
						}
					}

					// Create our custom message
					$custom_message = get_the_title() . ' ' . $hash_tags;
					update_post_meta( $post->ID, '_wpas_mess', $custom_message );
				}
			}
		}
	}

	// Save that message
	public function custom_message_save() {
		add_action( 'save_post', 'publicize_hashtags' );
	}

}

// If Jetpack is there, and publicize is active, we use this.
if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'publicize' ) ) {
	new LWTV_Jetpack();
}
