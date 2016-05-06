<?php
/*
Plugin Name: Symbolicons Settings
Plugin URI: http://lezwatchtv.com/
Description: Settings page to show you what Symbol Icons we have
Version: 1.0
Author: Mika Epstein
Author URI: http://ipstenu.org/
Author Email: ipstenu@halfelf.org

  Copyright (C) 2016 Mika Epstein.

*/

class SymboliconsSettings {

    public function __construct() {
        add_action( 'init', array( &$this, 'init' ) );
    }

    public function init() {
        add_action( 'admin_menu', array( $this, 'add_settings_page'));
    }

	// Sets up the settings page
	public function add_settings_page() {
		$page = add_theme_page(__('Fonticodes'), __('Fonticodes'), 'edit_posts', 'fonticodes', array($this, 'settings_page'));
	}

	// Content of the settings page
	function settings_page() {
		?>
		<div class="wrap">
		<h2>Symbolicons Settings</h2>	
			
		<?php

		if ( !file_exists( get_stylesheet_directory().'/images/symbolicons/' ) ) {
			echo '<p>Your theme does not appear to have the symbolicons folder included, so you can\'t use them. How sad. It should be installed in <code>'.get_stylesheet_directory().'/images/symbolicons/</code> for this to work.';

		} else {
			echo '<p>It\'s installed!</p>';
		}
	}
}

new SymboliconsSettings();