<?php
/**
 * LezWatch TV Custom Queries
 *
 * Custom Query Variables that let us have special funky town pages.
 *
 * Version:	 1.0
 * Author:	  Mika Epstein
 * Author URI:  https://halfelf.org
 *
 * @package LezWatch TV Theme
 * @author Mika Epstein
 * @license GPL-2.0+
 *
 */

// if this file is called directly abort
if ( ! defined('WPINC' ) ) {
	die;
}

class LWTVG_Query_Vars {

	// Constant for the query arguments we allow
	public $lez_query_args = array();

	/**
	 * Construct
	 * Runs the Code
	 *
	 * @since 1.0
	 */
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );

		$this->lez_query_args = array(
			'newest'	    => 'newtype',
			'role'      => 'roletype',
			'star'      => 'starcolor',
			'stats'     => 'statistics',
			'this-year' => 'thisyear',
			'thumbs'    => 'thumbscore',
		);
	}

	/**
	 * Main Plugin setup
	 *
	 * Adds actions, filters, etc. to WP
	 *
	 * @access public
	 * @return void
	 * @since 1.0
	 */
	function init() {
		// Plugin requires permalink usage - Only setup handling if permalinks enabled
		if ( get_option('permalink_structure') != '' ) {

			// tell WP not to override
			add_action ('query_vars', array($this, 'query_vars'));

			foreach( $this->lez_query_args as $slug => $query ) {
			    add_rewrite_rule(
			        '^'.$slug.'/([^/]+)/?$',
			        'index.php?pagename='.$slug.'&'.$query.'=$matches[1]',
			        'top'
			    );
			    add_rewrite_rule(
			        '^'.$slug.'/([^/]+)/page/([0-9]+)?/?$',
			        'index.php?pagename='.$slug.'&'.$query.'=$matches[1]&paged=$matches[2]',
			        'top'
			    );
			}

			// add filter for page
			add_filter( 'page_template', array( $this, 'page_template' ) );
			
			// Pluralization of Characters
			add_rewrite_rule(
				'^characters/?$',
				'index.php?post_type=post_type_characters&args=$matches[1]',
				'top'
			);

			// Pluralization of Shows
			add_rewrite_rule(
				'^shows/?$',
				'index.php?post_type=post_type_shows&args=$matches[1]',
				'top'
			);

		} else {
			add_action( 'admin_notices', array( $this, 'admin_notice_permalinks' ) );
		}
	}

	/**
	 * No Permalinks Notice
	 *
	 * @since 1.0
	 */
	public function admin_notice_permalinks() {
		echo '<div class="error"><p><strong>LezWatch TV Query Vars</strong> require you to use custom permalinks.</p></div>';
	}

	/**
	 * Add the query variables so WordPress won't override it
	 *
	 * @return $vars
	 */
	function query_vars( $vars ){
		foreach ( $this->lez_query_args as $argument ) {
			$vars[] = $argument;
		}
		return $vars;
	}

	/**
	 * Adds a custom template to the query queue.
	 *
	 * @return $templates
	 */
	function page_template( $templates = "" ){
		global $wp_query, $post;

		if ( array_key_exists( $post->post_name, $this->lez_query_args ) )
			$the_template = $this->lez_query_args[$post->post_name].'.php';

		foreach ( $this->lez_query_args as $argument ) {
			if( isset( $wp_query->query[$argument] ) ) {
				$templates = get_stylesheet_directory() . '/page-templates/' . $the_template;
			}
		}

		return $templates;
	}

}

new LWTVG_Query_Vars();