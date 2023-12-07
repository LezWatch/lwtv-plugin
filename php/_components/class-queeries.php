<?php
/**
 * LWTV\_Components\Queeries class.
 *
 * @package LWTV
 */

namespace LWTV\_Components;

use LWTV\Queeries\Is_Actor_Queer;
use LWTV\Queeries\Is_Actor_Trans;
use LWTV\Queeries\Is_Show_On_Air;
use LWTV\Queeries\Post_Meta_And_Tax;
use LWTV\Queeries\Post_Meta;
use LWTV\Queeries\Post_Type;
use LWTV\Queeries\Related_Posts_By_Tag;
use LWTV\Queeries\Tax_Two;
use LWTV\Queeries\Taxonomy;
use LWTV\Queeries\WP_Meta;

/**
 * Class for adding primary theme support.
 *
 * Exposes template tags
 *
 */
class Queeries implements Component, Templater {

	/**
	 * Init the component. Hooks go in here.
	 *
	 * @return void
	 */
	public function init(): void {
		//
	}

	/**
	 * Retrieve the template tags.
	 *
	 * @return array
	 */
	public function get_template_tags(): array {
		return array(
			'is_actor_queer'           => array( $this, 'is_actor_queer' ),
			'is_actor_trans'           => array( $this, 'is_actor_trans' ),
			'is_show_on_air'           => array( $this, 'is_show_on_air' ),
			'queery_post_meta_and_tax' => array( $this, 'queery_post_meta_and_tax' ),
			'queery_post_meta'         => array( $this, 'queery_post_meta' ),
			'queery_post_type'         => array( $this, 'queery_post_type' ),
			'get_related_posts_by_tag' => array( $this, 'get_related_posts_by_tag' ),
			'queery_tax_two'           => array( $this, 'queery_tax_two' ),
			'queery_taxonomy'          => array( $this, 'queery_taxonomy' ),
			'queery_wp_meta'           => array( $this, 'queery_wp_meta' ),
		);
	}

	public function is_actor_queer( $the_id ) {
		return ( new Is_Actor_Queer() )->make( $the_id );
	}

	public function is_actor_trans( $the_id ) {
		return ( new Is_Actor_Trans() )->make( $the_id );
	}

	public function is_show_on_air( $show_id, $year ) {
		return ( new Is_Show_On_Air() )->make( $show_id, $year );
	}

	public function queery_post_meta_and_tax( $post_type, $key, $value, $taxonomy, $field, $terms, $compare = '=', $operator = 'IN' ) {
		return ( new Post_Meta_And_Tax() )->make( $post_type, $key, $value, $taxonomy, $field, $terms, $compare, $operator );
	}

	public function queery_post_meta( $post_type, $key, $value, $compare = '=' ) {
		return ( new Post_Meta() )->make( $post_type, $key, $value, $compare );
	}

	public function queery_post_type( $post_type, $page = 0 ) {
		return ( new Post_Type() )->make( $post_type, $page );
	}

	public function get_related_posts_by_tag( $post_type, $slug ) {
		return ( new Related_Posts_By_Tag() )->make( $post_type, $slug );
	}

	public function queery_tax_two( $post_type, $taxonomy1, $field1, $terms1, $taxonomy2, $field2, $terms2, $operator1 = 'IN', $operator2 = 'IN', $relation = 'AND' ) {
		return ( new Tax_Two() )->make( $post_type, $taxonomy1, $field1, $terms1, $taxonomy2, $field2, $terms2, $operator1, $operator2, $relation );
	}

	public function queery_taxonomy( $post_type, $taxonomy, $field, $term, $operator = 'IN' ) {
		return ( new Taxonomy() )->make( $post_type, $taxonomy, $field, $term, $operator );
	}

	public function queery_wp_meta( $post_type, $slug ) {
		return ( new WP_Meta() )->make( $post_type, $slug );
	}
}
