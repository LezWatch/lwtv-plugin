<?php
/**
 * Name: Affiliate Code for Vimeo
 * Description: Automagical affiliate things
 */


class LWTV_Affiliate_Vimeo {
	function show_ads( $post_id, $type ) {

		// Return the proper output
		switch ( $type ) {
			case "text":
				return self::output_text( $post_id );
			case "widget":
			default:
				return self::output_widget( $post_id );
				break;
		}
	}

	/**
	 * Figure out which ad we really want to show....
	 */
	function output_widget( $post_id ) {

		// Code Here.
	}

}

new LWTV_Affiliate_Vimeo();
