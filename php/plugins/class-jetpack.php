<?php
/*
 * Jetpack for WordPress hooks
 * @package lwtv-plugin
 */

namespace LWTV\Plugins;

// Bail early if Jetpack isn't active.
if ( ! class_exists( 'Jetpack' ) ) {
	return;
}

class Jetpack {

	public function __construct() {
		// Custom search.
		add_action( 'init', array( $this, 'init_jetpack_search_filters' ) );

		// Jetpack extra images.
		add_action( 'admin_init', array( $this, 'jetpack_external_images' ) );

		// Disable jetpack sharing counts to not phone home to Facebook.
		add_filter( 'jetpack_sharing_counts', '__return_false' );

		// Integrate spam checkers with Jetpack.
		add_filter( 'jetpack_contact_form_is_spam', array( $this, 'jetpack_spammers' ), 11, 2 );
		add_filter( 'jetpack_contact_form_is_spam', array( $this, 'jetpack_harassment' ), 11, 2 );
	}

	/**
	 * Jetpack Search Filters
	 * We want to make sure we get post types in there.
	 */
	public function init_jetpack_search_filters() {
		if ( class_exists( 'Jetpack_Search' ) ) {
			// phpcs:disable
			\Jetpack_Search::instance()->set_filters( [
				'Content Type' => [
					'type'  => 'post_type',
					'count' => 10,
				],
				'Categories'   => [
					'type'     => 'taxonomy',
					'taxonomy' => 'category',
					'count'    => 10,
				],
				'Tags'         => [
					'type'     => 'taxonomy',
					'taxonomy' => 'post_tag',
					'count'    => 10,
				],
			] );
			// phpcs:enable
		}
	}

	/**
	 * [jetpack_spammers description]
	 * @param  boolean $is_spam   Default spam decision
	 * @param  array   $form      The form data
	 * @return boolean            If the person is spam
	 */
	public function jetpack_spammers( $is_spam, $form ) {
		// Bail early if already spam or if the new feature made it...
		if ( $is_spam ) {
			return $is_spam;
		}

		if ( wp_check_comment_disallowed_list( $form['comment_author'], $form['comment_author_email'], $form['comment_author_url'], $form['comment_content'], $form['user_ip'], $form['user_agent'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * [jetpack_harassment description]
	 * @param  boolean $is_spam   Default spam decision
	 * @param  array   $form      The form data
	 * @return boolean $is_spam   If the person is spam
	 */
	public function jetpack_harassment( $is_spam, $form ) {
		// Bail early if already spam
		if ( $is_spam ) {
			return $is_spam;
		}

		$badlist    = array();
		$disallowed = lwtv_plugin()->get_spammers_list();

		// Check the list for valid emails. Add the email _USERNAME_ to the list
		foreach ( $disallowed as $spammer ) {
			if ( is_email( $spammer ) ) {
				$emailparts = explode( '@', $spammer );
				$username   = $emailparts[0];
				$badlist[]  = $username;
			}
		}

		// Check if the comment author name matches an email we've banned
		// You'd think we didn't have to do this but ...
		if ( in_array( $form['comment_author'], $badlist, true ) ) {
			return true;
		}

		// Check if the email username is one of the bad ones
		// Get a true/falsy
		$is_spammer = lwtv_plugin()->is_spammer( $form['comment_author_email'] );
		if ( $is_spammer ) {
			return true;
		}

		return false;
	}

	/**
	 * Remove Jetpack's External Media feature.
	 *
	 * Removes for non-posts because we don't want it for actors/etc.
	 */
	public function jetpack_external_images() {
		global $pagenow, $post;

		$no_images_for = array( 'post_type_shows', 'post_type_players', 'post_type_actors' );

		// phpcs:ignore WordPress.Security.NonceVerification
		if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) {

			// phpcs:ignore WordPress.Security.NonceVerification
			$post_type = get_post_type( $_GET['post'] );

			if ( in_array( $post_type, $no_images_for, true ) ) {
				add_action(
					'enqueue_block_editor_assets',
					function () {
						$disable_external_media = <<<JS
							document.addEventListener( 'DOMContentLoaded', function() {
							wp.hooks.removeFilter( 'blocks.registerBlockType', 'external-media/individual-blocks' );
							wp.hooks.removeFilter( 'editor.MediaUpload', 'external-media/replace-media-upload' );
							} );
						JS;
						wp_add_inline_script( 'jetpack-blocks-editor', $disable_external_media );
					}
				);
			}
		}
	}
}
