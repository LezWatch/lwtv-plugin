<?php
/*
 * Privacy related code, to handle when we make a post Private in order to hide it for
 * when someone asks us to remove the post.
 *
 * Generally this is when an actor wants things removed. We always respect their wishes.
 *
 * @since 1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Privacy_Code {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		add_action( 'edit_form_after_title', array( $this, 'admin_notices' ) );
	}

	/**
	 * Admin Notices
	 */
	public function admin_notices() {
		$message  = '';
		$type     = 'updated';
		$dashicon = 'warning';
		$post     = get_post();
		$status   = get_post_status( $post->ID );

		switch ( $post->post_type ) {
			case 'post_type_actors':
				if ( 'private' === $status ) {
					$message = 'This post is private, to prevent non-logged in users from seeing it. Likely this was done to deal with a privacy removal request. Please check the Internal Notes before making public.';
					$type    = 'error';
				}
				break;
		}

		if ( $message ) {
			printf( '<div class="notice %1$s"><p><span class="dashicons dashicons-%2$s"></span> %3$s</p></div>', esc_attr( $type ), esc_attr( $dashicon ), esc_html( $message ) );
		}
	}
}

new LWTV_Privacy_Code();
