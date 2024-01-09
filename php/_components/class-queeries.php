<?php
/**
 * LWTV\_Components\Queeries class.
 *
 * @package LWTV
 */

namespace LWTV\_Components;

use LWTV\Queeries\Get_ID_From_Slug;
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
			'get_id_from_slug'         => array( $this, 'get_id_from_slug' ),
		);
	}

	/**
	 * Is an Actor Queer?
	 *
	 * @param  int   $the_id
	 * @return bool
	 */
	public function is_actor_queer( $the_id ): bool {
		return ( new Is_Actor_Queer() )->make( $the_id );
	}

	/**
	 * Is actor trans?
	 *
	 * @param  int   $the_id
	 * @return bool
	 */
	public function is_actor_trans( $the_id ): bool {
		return ( new Is_Actor_Trans() )->make( $the_id );
	}

	/**
	 * Is Show on air?
	 *
	 * @param  int   $the_id
	 * @return bool
	 */
	public function is_show_on_air( $show_id, $year ): bool {
		return ( new Is_Show_On_Air() )->make( $show_id, $year );
	}

	/**
	 * Query Post Meta and taxonomy
	 *
	 * @param  string $post_type
	 * @param  string $key
	 * @param  string $value
	 * @param  string $taxonomy
	 * @param  string $field
	 * @param  string $terms
	 * @param  string $compare
	 * @param  string $operator
	 *
	 * @return WP_Query
	 */
	public function queery_post_meta_and_tax( $post_type, $key, $value, $taxonomy, $field, $terms, $compare = '=', $operator = 'IN' ) {
		return ( new Post_Meta_And_Tax() )->make( $post_type, $key, $value, $taxonomy, $field, $terms, $compare, $operator );
	}

	/**
	 * Query Post Meta
	 *
	 * @param  string $post_type
	 * @param  string $key
	 * @param  string $value
	 * @param  string $compare
	 *
	 * @return WP_Query
	 */
	public function queery_post_meta( $post_type, $key, $value, $compare = '=' ) {
		return ( new Post_Meta() )->make( $post_type, $key, $value, $compare );
	}

	/**
	 * Query Post Type
	 *
	 * @param  string $post_type
	 * @param  int    $page
	 *
	 * @return WP_Query
	 */
	public function queery_post_type( $post_type, $page = 0 ) {
		return ( new Post_Type() )->make( $post_type, $page );
	}

	/**
	 * Get Related Posts by Tag
	 *
	 * @param  string $post_type
	 * @param  string $slug
	 *
	 * @return WP_Query
	 */
	public function get_related_posts_by_tag( $post_type, $slug ) {
		return ( new Related_Posts_By_Tag() )->make( $post_type, $slug );
	}

	/**
	 * Query TWO Taxonomies for overlap
	 *
	 * @param  string $post_type
	 * @param  string $taxonomy1
	 * @param  string $field1
	 * @param  string $terms1
	 * @param  string $taxonomy2
	 * @param  string $field2
	 * @param  string $terms2
	 * @param  string $operator1
	 * @param  string $operator2
	 * @param  string $relation
	 *
	 * @return WP_Query
	 */
	public function queery_tax_two( $post_type, $taxonomy1, $field1, $terms1, $taxonomy2, $field2, $terms2, $operator1 = 'IN', $operator2 = 'IN', $relation = 'AND' ) {
		return ( new Tax_Two() )->make( $post_type, $taxonomy1, $field1, $terms1, $taxonomy2, $field2, $terms2, $operator1, $operator2, $relation );
	}

	/**
	 * Query Taxonomy
	 *
	 * @param  string $post_type
	 * @param  string $taxonomy
	 * @param  string $field
	 * @param  string $term
	 * @param  string $operator
	 *
	 * @return WP_Query
	 */
	public function queery_taxonomy( $post_type, $taxonomy, $field, $term, $operator = 'IN' ) {
		return ( new Taxonomy() )->make( $post_type, $taxonomy, $field, $term, $operator );
	}

	/**
	 * Query WP Meta (not currently used)
	 *
	 * @param  string $post_type
	 * @param  string $slug
	 *
	 * @return WP_Query
	 */
	public function queery_wp_meta( $post_type, $slug ) {
		return ( new WP_Meta() )->make( $post_type, $slug );
	}

	/**
	 * Get Post ID from slug
	 *
	 * @param  string $the_slug
	 * @return string
	 */
	public function get_id_from_slug( $the_slug ) {
		return ( new Get_ID_From_Slug() )->make( $the_slug );
	}
}
