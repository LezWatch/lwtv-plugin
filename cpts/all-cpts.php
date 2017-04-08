<?php
/*
 * All CPTs Code
 *
 * Code that runs on all custom post types.
 *
 * @since 1.5
 * Authors: Mika Epstein
 */


/**
 * class LWTV_All_CPTs
 */
class LWTV_All_CPTs {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'featured_images' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts') );
		add_action( 'get_header' , array( $this, 'admin_notices' ), 20 );
	}

	/**
	 * Rename Featured Images
	 */
	public function featured_images() {
		$post_type_args = array(
		   'public'   => true,
		   '_builtin' => false
		);
		$post_types = get_post_types( $post_type_args, 'objects' );
		foreach ( $post_types as $post_type ) {

			$type = $post_type->name;
			$name = $post_type->labels->singular_name;

			// change the default "Featured Image" metabox title
			add_action('do_meta_boxes', function() use ( $type, $name ) {
				remove_meta_box( 'postimagediv', $type, 'side' );
				add_meta_box('postimagediv', $name.' Image', 'post_thumbnail_meta_box', $type, 'side');
			});

			// change the default "Set Featured Image" text
			add_filter( 'admin_post_thumbnail_html', function( $content ) use ( $type, $name ) {
				global $current_screen;
				if( !is_null($current_screen) && $type == $current_screen->post_type ) {
				    // Get featured image size
				    global $_wp_additional_image_sizes;
				    $genesis_image_size = rtrim( str_replace( 'post_type_', '', $type ), 's' ).'-img';
				    if ( isset( $_wp_additional_image_sizes[ $genesis_image_size ] ) ) {
				        $content = '<p>Image Size: ' . $_wp_additional_image_sizes[$genesis_image_size]['width'] . 'x' . $_wp_additional_image_sizes[$genesis_image_size]['height'] . 'px</p>' . $content;
				    }
					$content = str_replace( __( 'featured' ), strtolower( $name ) , $content);
				}
				return $content;
			});
		}
	}

	/**
	 * Front End CSS Customizations
	 */
	public function wp_enqueue_scripts( ) {
		wp_register_style( 'cpt-shows-styles', plugins_url( 'shows.css', __FILE__ ) );

		if( is_single() && get_post_type() == 'post_type_shows' ){
			wp_enqueue_style( 'cpt-shows-styles' );
		}
	}

	/*
	 * Admin Notices
	 */
	function admin_notices() {

		if ( !get_post() ) return;

		$message    = '';
		$type       = 'updated';
		$post       = get_post();

		$content    = get_post_field( 'post_content', $post->ID );
		$word_count = str_word_count( strip_tags( $content ) );

		switch ( $post->post_type ) {
			case 'post_type_shows':
				$worthit    = get_post_meta( $post->ID, 'lezshows_worthit_details', true );

				if ( $worthit < '1' ) {
					$type     = 'notice-info';
					$message  = 'Is this show worth watching? We don\'t know.';
					$dashicon = 'heart';

					if ( $word_count < '100' ) {
						$type     = 'notice-error';
						$message  = 'We clearly know nothing about this show.';
						$dashicon = 'warning';
					} elseif ( $word_count < '200' ) {
						$type     = 'notice-warning';
						$message  = 'This post is a stub. Please edit it and make it more awesome.';
						$dashicon = 'info';
					}
				}
				break;
		}

		if ( $message && is_user_logged_in() && is_single() ) {
			printf( '<div class="wrap"><div class="notice %1$s"><p><span class="dashicons dashicons-%2$s"></span> %3$s</p></div></div>', esc_attr( $type ), esc_attr( $dashicon ), esc_html( $message ) );
		}

	}

}
new LWTV_All_CPTs();