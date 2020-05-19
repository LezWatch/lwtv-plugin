<?php
/**
 * Imagify Tweaks
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Imagify {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'imagify_auto_optimize_attachment', array( $this, 'no_optimize_gif' ) );
		add_filter( 'intermediate_image_sizes_advanced', array( $this, 'disable_upload_sizes_gifs' ), 10, 2 );
	}

	/**
	 * Prevent automatic optimization for GIF.
	 * URI:  https://github.com/wp-media/imagify-helpers/tree/master/optimization/imagify-no-auto-optimize-gif/
	 * Copyright SAS WP MEDIA 2018
	 *
	 * @author Grégory Viguier
	 * @author Caspar Hübinger
	 *
	 * @param  bool  $optimize      True to optimize, false otherwise.
	 * @param  int   $attachment_id Attachment ID.
	 * @param  array $metadata      An array of attachment meta data.
	 * @return bool
	 */
	public function no_optimize_gif( $optimize, $attachment_id, $metadata ) {
		if ( ! $optimize ) {
			return false;
		}

		$mime_type = get_post_mime_type( $attachment_id );

		return 'image/gif' !== $mime_type;
	}


	/**
	 * Disable Upload Sizes for animated gifs
	 *
	 * https://wordpress.stackexchange.com/questions/229675/disable-resizing-of-gif-when-uploaded
	 *
	 * @param  array $sizes    All the sizes
	 * @param  array $metadata All the metadata for the media
	 * @return array           The sizes array
	 */
	public function disable_upload_sizes_gifs( $sizes, $metadata ) {

		// Get filetype data.
		$filetype = wp_check_filetype( $metadata['file'] );
		// Get file size
		$upload_dir = wp_upload_dir();
		$filesize   = (int) filesize( $upload_dir['basedir'] . '/' . $metadata['file'] );

		// Check if is gif AND over 1 meg (which means animated for us)
		if ( 'image/gif' === $filetype['type'] && $filesize >= 1048576 ) {
			// unset everything **except** thumbnails
			foreach ( $sizes as $size => $details ) {
				if ( 'thumbnail' !== $size ) {
					unset( $sizes[ $size ] );
				}
			}
		}

		// Return sizes left
		return $sizes;
	}

}

new LWTV_Imagify();
