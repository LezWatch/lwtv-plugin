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
        add_action( 'admin_menu', array( $this, 'add_settings_page') );
    }

	// Sets up the settings page
	public function add_settings_page() {
		$page = add_theme_page(__('Symbolicons'), __('Symbolicons'), 'edit_posts', 'symbolicons', array($this, 'settings_page'));
	}

	// Content of the settings page
	function settings_page() {
		?>
		<div class="wrap">

		<style>
			span.cmb2-icon {
				width: 80px;
			    display: inline-block;
			    vertical-align: top;
			    margin: 10px;
			    word-wrap: break-word;
			}
			span.cmb2-icon svg {
			    width: 75px;
			    height: 75px;
			}
			span.cmb2-icon svg * {
				fill: #444;
			}
		</style>

		<h2>Symbolicons</h2>

		<?php

		$imagepath = plugin_dir_path( __FILE__ ). '/symbolicons/';

		if ( !file_exists( $imagepath ) && !is_dir( $imagepath ) ) {
			echo '<p>Your site does not appear to have the symbolicons folder included, so you can\'t use them. How sad. It should be installed in <code>'.plugin_dir_path( __FILE__ ).'/symbolicons/</code> for this to work.';

		} else {

			echo '<p>The following are all the symbolicons we have to chose from and their file names. Let this help you be more better with your iconing.</p>';

			foreach( glob( $imagepath.'*' ) as $filename ){
				$image = file_get_contents( $filename );
				$name  = str_replace( $imagepath, '' , $filename );
				$name  = str_replace( '.svg', '', $name );
				echo '<span role="img" class="cmb2-icon">' . $image . $name .'</span>';
			}
		}
	}

}
new SymboliconsSettings();