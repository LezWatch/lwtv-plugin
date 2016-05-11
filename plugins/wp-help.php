<?php
/*
Description: Boostrap file to make a couple tweaks to wp-help
Version: 1.0
Author: Mika Epstein
*/

function lez_wp_help_css(){
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

// Handle the CSS for this
function wph_lez_scripts( $hook ) {
	add_action( 'admin_print_scripts', 'lez_wp_help_css' );
}
add_action( 'admin_print_styles-toplevel_page_wp-help-documents', 'wph_lez_scripts', 10 );