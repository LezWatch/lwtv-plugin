<?php
/**
 * Name: Social Media Stuff
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Social_Media {

	public function __construct() {
		add_action( 'wp_head', array( $this, 'mastadon' ), 10 );
		add_filter( 'wp_nav_menu_objects', array( $this, 'add_rel_me' ), 10, 2 );
	}

	public function preload_styles() {
		echo '<link rel="me" href="https://mastodon.social/@lezwatchtv"/>';
	}

	/** Add rel="me" to social menu items. */
	public function add_rel_me( $items, $args ) {
		if ( 'follow-us' === $args->menu->name ) {
			foreach ( $items as $i ) {
				$i->xfn .= ' me';
			}
		}
			return $items;
	}
}

new LWTV_Social_Media();
