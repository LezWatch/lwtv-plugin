<?php
	
/*
 * Symbolicons Fonts.
 *
 */
 
function symbolicons_fonts() {
    wp_enqueue_style( 'symbolicons', plugins_url( 'ss-symbolicons-block/webfonts/ss-symbolicons-block.css' , __FILE__ ) );}
add_action( 'wp_enqueue_scripts', 'symbolicons_fonts' );
