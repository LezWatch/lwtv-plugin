<?php
/*
Plugin Name: Symbolicons Bootstrap
Plugin URI:  https://fortawesome.com/sets/symbolicons-block
Description: Boostrap file to load the symbolicons.
Version: 1.0
Author: Mika Epstein
*/

//add_action( 'wp_enqueue_scripts', 'symbolicons_fonts' );     // Front end
//add_action( 'admin_enqueue_scripts', 'symbolicons_fonts' );  // Back end
function symbolicons_fonts() {
    wp_enqueue_style( 'symbolicons', plugins_url( 'ss-symbolicons-block/webfonts/ss-symbolicons-block.css' , __FILE__ ) );
}