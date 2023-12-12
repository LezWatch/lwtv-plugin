<?php
/*
 * Plugins
 */
namespace LWTV\_Components;

use LWTV\Plugins\Cache;
use LWTV\Plugins\CMB2;
use LWTV\Plugins\Comment_Probation;
use LWTV\Plugins\FacetWP;
use LWTV\Plugins\Gravity_Forms;
use LWTV\Plugins\Gutenberg;
use LWTV\Plugins\Jetpack;
use LWTV\Plugins\Related_Posts_By_Taxonomy;
use LWTV\Plugins\Yoast;

class Plugins implements Component, Templater {

	/*
	 * Init
	 *
	 * Call the sub plugins
	 */
	public function init(): void {
		new Comment_Probation();
		new CMB2();
		new FacetWP();
		new Gravity_Forms();
		new Gutenberg();
		new Jetpack();
		new Related_Posts_By_Taxonomy();
		new Yoast();
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
			'collect_cache_urls_for_characters' => array( $this, 'collect_cache_urls_for_characters' ),
			'clean_cache_urls'                  => array( $this, 'clean_cache_urls' ),
			'get_cmb2_terms_list'               => array( $this, 'get_cmb2_terms_list' ),
			'get_select2_defaults'              => array( $this, 'get_select2_defaults' ),
			'save_select2_taxonomy'             => array( $this, 'save_select2_taxonomy' ),
		);
	}

	/**
	 * Collect the URLs we're going to flush for characters
	 *
	 * @param  int     $post_id ID of the character
	 * @return array   array of URLs
	 */
	public function collect_cache_urls_for_characters( $post_id ) {
		return ( new Cache() )->collect_urls_for_characters( $post_id );
	}

	/**
	 * Collect the URLs we're going to flush for characters
	 *
	 * @param  array  $clear_urls - Arrays of URLs to clean
	 */
	public function clean_cache_urls( $clear_urls ) {
		return ( new Cache() )->clean_urls( $clear_urls );
	}

	/**
	 * Get a list of terms
	 *
	 * @param  array  $taxonomies
	 * @param  string $query_args
	 * @return array
	 */
	public function get_cmb2_terms_list( $taxonomies, $query_args = '' ) {
		return ( new CMB2() )->get_cmb2_terms_list( $taxonomies, $query_args );
	}

	/**
	 * Save Select2 Taxonomy
	 *
	 * @param  int    $post_id
	 * @param  string $postmeta
	 * @param  string $taxonomy
	 * @return void
	 */
	public function save_select2_taxonomy( $post_id, $postmeta, $taxonomy ) {
		( new CMB2() )->select2_taxonomy_save( $post_id, $postmeta, $taxonomy );
	}

	/**
	 * Get default data for some odd CMB2 things using select2
	 *
	 * @param  string  $postmeta the name of the postmeta used by CMB2
	 * @param  string  $taxonomy the name of the taxonomy we're using
	 * @param  integer $post_id  post ID
	 * @param  boolean $none     does it have a 'none'?
	 *
	 * @return array             An array of Term IDs
	 */
	public function get_select2_defaults( $postmeta, $taxonomy, $post_id = 0, $none = false ) {
		return ( new CMB2() )->get_select2_defaults( $postmeta, $taxonomy, $post_id, $none );
	}
}
