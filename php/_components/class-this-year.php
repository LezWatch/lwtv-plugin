<?php
/**
 * Functions that power the "This Year" pages
 *
 * @package LezWatch.TV
 */

namespace LWTV\_Components;

use LWTV\This_Year\Display;

class This_Year implements Component, Templater {

	/**
	 * Array of Data types and their associated classes.
	 */
	public const DATA_CLASS_MATCHER = array(
		'characters-on-air' => 'Characters_List',
		'dead-characters'   => 'Characters_Dead',
		'shows-on-air'      => 'Shows_List',
		'new-shows'         => 'Shows_List',
		'canceled-shows'    => 'Shows_List',
		'overview'          => 'Overview',
		'chart'             => 'Characters_List',
	);

	/**
	 * Array of Data types and their associated classes.
	 */
	public const FORMAT_CLASS_MATCHER = array(
		'characters-on-air' => 'Characters',
		'dead-characters'   => 'Dead',
		'shows-on-air'      => 'Shows',
		'new-shows'         => 'Shows',
		'canceled-shows'    => 'Shows',
		'overview'          => 'Overview',
		'chart'             => 'Chart',
	);

	/*
	 * Init
	 *
	 * Call the sub plugins
	 */
	public function init() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wpseo_register_extra_replacements', array( $this, 'yoast_seo_register_extra_replacements' ) );

		// Plugin requires permalink usage - Only setup handling if permalinks enabled
		if ( '' !== get_option( 'permalink_structure' ) ) {

			// tell WP not to override query vars
			add_action( 'query_vars', array( $this, 'query_vars' ) );

			// add filter for pages
			add_filter( 'page_template', array( $this, 'page_template' ) );

			// Add rewrite rules
			add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'admin_notice_permalinks' ) );
		}
	}

	/**
	 * Gets tags to expose as methods accessible through `lwtv_plugin()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function get_template_tags(): array {
		return array(
			'get_this_year_display' => array( $this, 'get_this_year_display' ),
		);
	}

	/**
	 * Build the stuff for this year
	 *
	 * @param string $year
	 */
	public function get_this_year_display( $year ) {
		return ( new Display() )->make( $year );
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
		\wpseo_register_var_replacement( '%%thisyear%%', array( $this, 'yoast_retrieve_year_replacement' ), 'basic', 'The year.' );
	}

	/*
	 * Extra Meta Variables for Yoast and Year pages
	 *
	 * The type of stats page we're on
	 */
	public function yoast_retrieve_year_replacement() {
		$this_year = get_query_var( 'thisyear', 'none' );
		$return    = ( 'none' !== $this_year ) ? ucfirst( $this_year ) : gmdate( 'Y' );
		$return    = '(' . $return . ')';
		return $return;
	}

	/**
	 * Add Rewrite Rules
	 *
	 * @return void
	 */
	public function add_rewrite_rules() {
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
	}
}
