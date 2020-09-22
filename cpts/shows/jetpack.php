<?php
/*
 * Name: Jetpack
 * Desc: Custom Jetpack things
 *
 * This doesn't work properly because of https://github.com/Automattic/jetpack/issues/11869
 */

class LWTV_CPT_Show_Jetpack {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'wpas_default_message', array( $this, 'publicize_default_message' ) );
	}

	/**
	 * Create the default publicize message
	 * @param  string $message The message
	 * @return string          A new message
	 */
	public function publicize_default_message( $message ) {

		$post = get_post();

		$show_name = trim( preg_replace( '~\([^)]+\)~', '', $post->post_title ) );
		$show_name = str_replace( ' & ', ' and ', $show_name );
		$show_name = sanitize_title( $show_name );
		$hashtag   = '#' . implode( '', array_map( 'ucfirst', explode( '-', $show_name ) ) );

		$publicize = 'A new show has been added to the database! ' . $post->post_title . ' ' . $hashtag;

		return $publicize;
	}

}

new LWTV_CPT_Show_Jetpack();
