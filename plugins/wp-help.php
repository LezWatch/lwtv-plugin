<?php
/*
Description: Boostrap file to make a couple tweaks to wp-help
Version: 1.0
Author: Mika Epstein
*/


// Handle the CSS for this
function wph_lez_scripts( $hook ) {
	wp_register_style( 'wph-lez-styles', plugins_url('wp-help.css', __FILE__ ) );
	wp_enqueue_style( 'wph-lez-styles' );
}
add_action( 'admin_print_styles-toplevel_page_wp-help-documents', 'wph_lez_scripts', 10 );