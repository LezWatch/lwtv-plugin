<?php
/*
Library: WP Help
Description: Boostrap file to make a couple tweaks to wp-help
Version: 1.0
Author: Mika Epstein
*/

class LWTV_WP_Help{

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_print_scripts-toplevel_page_wp-help-documents', array( $this, 'wp_help_css' ) );
	}

	/**
	 * CSS to customize WP Help
	 *
	 * @since 1.0
	 */
	public function wp_help_css( ){
		echo '
		<style>
			#cws-wp-help-document {
			    max-width: 600px;
			    margin-left: 300px;
			    background: white;
			    padding: 10px!important;
				border: 1px solid #ddd;
			}

			div#cws-wp-help-document p,
			#cws-wp-help-listing ul li {
			    font-size: 14px;
			}
		</style>
		';
	}


}

new LWTV_WP_Help();