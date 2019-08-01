<?php
/**
 * LezWatch.TV Custom Queries for This Year
 *
 * Version: 1.0
 *
 * @package LezWatch.TV Plugin
 *
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_This_Year_Query_Vars {


	/**
	 * Construct
	 * Runs the Code
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );

		// Some Yoasty things
		add_action( 'wpseo_register_extra_replacements', array( $this, 'yoast_seo_register_extra_replacements' ) );
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
	public function init() {
		// Plugin requires permalink usage - Only setup handling if permalinks enabled
		if ( '' !== get_option( 'permalink_structure' ) ) {

			// tell WP not to override query vars
			add_action( 'query_vars', array( $this, 'query_vars' ) );

			// add filter for pages
			add_filter( 'page_template', array( $this, 'page_template' ) );

			$views = array( 'characters-on-air', 'dead-characters', 'shows-on-air', 'new-shows', 'canceled-shows' );

			// Basic Rule
			add_rewrite_rule(
				'^this-year/([0-9]{4})/?$',
				'index.php?pagename=this-year&thisyear=$matches[1]',
				'top'
			);

			foreach ( $views as $a_view ) {
				add_rewrite_rule(
					'^this-year/' . $a_view . '/?$',
					'index.php?pagename=this-year&view=' . $a_view,
					'top'
				);

				add_rewrite_rule(
					'^this-year/([0-9]{4})/' . $a_view . '/?$',
					'index.php?pagename=this-year&thisyear=$matches[1]&view=' . $a_view,
					'top'
				);
			}
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
		echo '<div class="error"><p><strong>LezWatch.TV Query Vars</strong> require you to use custom permalinks.</p></div>';
	}

	/**
	 * Add the query variables so WordPress won't override it
	 *
	 * @return $vars
	 */
	public function query_vars( $vars ) {
		$vars[] = 'thisyear';
		$vars[] = 'view';
		return $vars;
	}

	/**
	 * Adds a custom template to the query queue.
	 *
	 * @return $templates
	 */
	public function page_template( $templates = '' ) {
		if ( isset( $wp_query->query['thisyear'] ) ) {
			$templates = get_stylesheet_directory() . '/page-templates/thisyear.php';
		}
		return $templates;
	}

	/*
	 * Extra Replacement Functions for Yoast SEO
	 */
	public function yoast_seo_register_extra_replacements() {
		wpseo_register_var_replacement( '%%thisyear%%', array( $this, 'yoast_retrieve_year_replacement' ), 'basic', 'The year.' );
	}

	/*
	 * Extra Meta Variables for Yoast and Year pages
	 *
	 * The type of stats page we're on
	 */
	public function yoast_retrieve_year_replacement() {
		$this_year = get_query_var( 'thisyear', 'none' );
		$return    = ( 'none' !== $this_year ) ? ucfirst( $this_year ) : date( 'Y' );
		$return    = '(' . $return . ')';
		return $return;
	}

}

new LWTV_This_Year_Query_Vars();
