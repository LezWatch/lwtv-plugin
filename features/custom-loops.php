<?php
/**
 * Name: Custom Loops
 * Description: Custom arrays and WP_Query calls that are repeated
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Loops
 *
 * Customize Loops
 *
 * @since 1.0
 */

class LWTV_Loops {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Nothing to see here
	}

	/**
	 * Determine if an actor is queer
	 *
	 * @access public
	 * @param mixed $the_id
	 * @return void
	 */
	public function is_actor_queer( $the_id ) {

		if ( ! isset( $the_id ) || 'post_type_actors' !== get_post_type( $the_id ) ) {
			return;
		}

		// Defaults
		$gender    = 'yes';
		$sexuality = 'yes';
		$is_queer  = 'no';

		// If the actor is cis, they may not be queer
		// Also 'undefined' isn't queer since we just don't know
		$straight_genders = array( 'cis-man', 'cis-woman', 'cisgender', 'undefined' );
		$gender_terms     = get_the_terms( $the_id, 'lez_actor_gender', true );
		if ( ! $gender_terms || is_wp_error( $gender_terms ) || has_term( $straight_genders, 'lez_actor_gender', $the_id ) ) {
			$gender = 'no';
		}

		// If the actor is heterosexual they may not be queer
		$straight_sexuality = array( 'heterosexual', 'unknown' );
		$sexuality_terms    = get_the_terms( $the_id, 'lez_actor_sexuality', true );
		if ( ! $sexuality_terms || is_wp_error( $sexuality_terms ) || has_term( $straight_sexuality, 'lez_actor_sexuality', $the_id ) ) {
			$sexuality = 'no';
		}

		// If either the gender or sexuality is queer, we have a queerio!
		if ( 'yes' === $sexuality || 'yes' === $gender ) {
			$is_queer = 'yes';
		}

		if ( 'private' === get_post_status( $the_id ) ) {
			$is_queer = 'no';
		}

		return $is_queer;
	}

	/**
	 * Determine if an actor is transgender IRL
	 *
	 * @access public
	 * @param  int $the_id - Post ID
	 * @return bool
	 */
	public function is_actor_trans( $the_id ) {

		if ( ! isset( $the_id ) || 'post_type_actors' !== get_post_type( $the_id ) ) {
			return;
		}

		// Defaults
		$is_trans  = 'no';
		$the_terms = '';

		// The gender terms this actor uses:
		$gender_terms = get_the_terms( $the_id, 'lez_actor_gender', true );

		// If there are terms, let's add the slugs to a list.
		if ( ! empty( $gender_terms ) && ! is_wp_error( $gender_terms ) ) {
			$the_terms = implode( ' ', wp_list_pluck( $gender_terms, 'slug' ) );
		}

		// If the string has 'trans' anywhere in it, we're trans!
		if ( false !== strpos( $the_terms, 'trans' ) ) {
			$is_trans = 'yes';
		}

		if ( 'private' === get_post_status( $the_id ) ) {
			$is_trans = 'no';
		}

		return $is_trans;
	}

	/**
	 * Determine if a show is on air
	 *
	 * @access public
	 * @param  int  $post_id - Post ID
	 * @param  int  $year    - Year they may be on air
	 * @return bool
	 */
	public function is_show_on_air( $post_id, $year ) {

		// Defaults
		$return    = false;
		$this_year = gmdate( 'Y' );

		// Get the data.
		if ( get_post_meta( $post_id, 'lezshows_airdates', true ) ) {
			$airdates = get_post_meta( $post_id, 'lezshows_airdates', true );
			// If the start is 'current' make it this year (though it really never should be.)
			if ( 'current' === $airdates['start'] ) {
				$airdates['start'] = $this_year;
			}

			// Setting 'end' to current for easier math later
			if ( 'current' === $airdates['finish'] ) {
				$airdates['finish'] = $this_year;
			}
		}

		if ( isset( $airdates ) ) {
			// if START is equal to or LESS than $year
			// AND if END is qual to or GREATER than $year
			// Then the show was on air.
			if ( $airdates['start'] <= $year && $airdates['finish'] >= $year ) {
				$return = true;
			}
		}

		return $return;
	}

	/*
	 * Taxonomy Array
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $taxonomy The taxonomy being searched
	 * @param array $term The term being searched for.
	 * @param string $operator Search operator. Default IN.
	 *
	 * @return array The WP_Query Array
	 */
	public function tax_query( $post_type, $taxonomy, $field, $term, $operator = 'IN' ) {
		$count  = wp_count_posts( $post_type )->publish;
		$queery = new WP_Query(
			array(
				'post_type'              => $post_type,
				'posts_per_page'         => $count,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'post_status'            => array( 'publish' ),
				'tax_query'              => array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => $field,
						'terms'    => $term,
						'operator' => $operator,
					),
				),
			)
		);
		wp_reset_query();
		return $queery;
	}

	/*
	 * Taxonomy Two Array
	 *
	 * This check is used for generating a query of posts that are in two taxonomies.
	 * For example you can use it to loop through dead queers who are non-binary
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $taxonomy1 The taxonomy being searched
	 * @param array $term1 The term being searched for.
	 * @param string $taxonomy2 The taxonomy being searched
	 * @param array $term2 The term being searched for.
	 * @param string $operator1 Search operator. Default IN.
	 * @param string $operator2 Search operator. Default IN.
	 *
	 * @return array The WP_Query Array
	 */
	public function tax_two_query( $post_type, $taxonomy1, $field1, $terms1, $taxonomy2, $field2, $terms2, $operator1 = 'IN', $operator2 = 'IN', $relation = 'AND' ) {
		$count = wp_count_posts( $post_type )->publish;
		$query = new WP_Query(
			array(
				'post_type'              => $post_type,
				'posts_per_page'         => $count,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'post_status'            => array( 'publish' ),
				'relation'               => $relation,
				'tax_query'              => array(
					array(
						'taxonomy' => $taxonomy1,
						'field'    => $field1,
						'terms'    => $terms1,
						'operator' => $operator1,
					),
					array(
						'taxonomy' => $taxonomy2,
						'field'    => $field2,
						'terms'    => $terms2,
						'operator' => $operator2,
					),
				),
			)
		);
		wp_reset_query();
		return $query;
	}

	/*
	 * Post Meta Array
	 *
	 * For when you need the whole post data
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $key The post meta key being searched for.
	 * @param string $value The post meta VALUE being searched for.
	 * @param string $compare Search operator. Default =
	 *
	 * @return array The WP_Query Array
	 */
	public function post_meta_query( $post_type, $key, $value, $compare = '=' ) {
		$count = wp_count_posts( $post_type )->publish;
		if ( '' !== $value ) {
			$query = new WP_Query(
				array(
					'post_type'              => $post_type,
					'post_status'            => array( 'publish' ),
					'orderby'                => 'title',
					'order'                  => 'ASC',
					'posts_per_page'         => $count,
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
					'meta_query'             => array(
						array(
							'key'     => $key,
							'value'   => $value,
							'compare' => $compare,
						),
					),
				)
			);
		} else {
			$query = new WP_Query(
				array(
					'post_type'              => $post_type,
					'post_status'            => array( 'publish' ),
					'orderby'                => 'title',
					'order'                  => 'ASC',
					'posts_per_page'         => $count,
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
					'meta_query'             => array(
						array(
							'key'     => $key,
							'compare' => $compare,
						),
					),
				)
			);
		}

		wp_reset_query();
		return $query;
	}

	/*
	 * WP Meta Query
	 *
	 * For when you need the whole post data
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $key The post meta key being searched for.
	 * @param string $value The post meta VALUE being searched for.
	 * @param string $compare Search operator. Default =
	 *
	 * @return array The WP_Query Array
	 */
	public function wp_meta_query( $key, $value, $compare = '=', $relation = 'AND' ) {

		global $wpdb;

		$query_args = array(
			'relation' => $relation,
			array(
				'key'     => $key,
				'value'   => $value,
				'compare' => $compare,
			),
		);
		$query      = new WP_Meta_Query( $query_args );

		$sql = $query->get_sql(
			'post',
			$wpdb->posts,
			'ID',
			null
		);

		return $sql;
	}

	/*
	 * Post Type Array
	 *
	 * Generate an array of all posts in a specific post type.
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 *
	 * @return array The WP_Query Array
	 */

	public function post_type_query( $post_type, $page = 0 ) {
		if ( 0 === $page ) {
			$count  = wp_count_posts( $post_type )->publish;
			$offset = 0;
		} else {
			$count  = 100;
			$offset = ( 100 * $page ) - 100;
		}
		$qarray = array(
			'post_type'              => $post_type,
			'posts_per_page'         => $count,
			'offset'                 => $offset,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status'            => array( 'publish' ),
		);
		$queery = new WP_Query( $qarray );
		wp_reset_query();
		return $queery;
	}

	/*
	 * Post Meta AND Taxonomy Query
	 *
	 * Function to generate an array of posts that have a specific post meta AND
	 * a specific taxonomy value. Useful for getting a list of all dead queers
	 * who are main characters (for example).
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $key The post meta key being searched for.
	 * @param string $value The post meta VALUE being searched for.
	 * @param string $taxonomy The taxonomy being searched
	 * @param array $term The term being searched for.
	 * @param string $compare Search operator for meta_query. Default =
	 * @param string $operator Search operator for tax_query. Default IN.
	 *
	 * @return array The WP_Query Array
	 */

	public function post_meta_and_tax_query( $post_type, $key, $value, $taxonomy, $field, $terms, $compare = '=', $operator = 'IN' ) {
		$count = wp_count_posts( $post_type )->publish;
		$query = new WP_Query(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => $count,
				'no_found_rows'  => true,
				'post_status'    => array( 'publish' ),
				'meta_query'     => array(
					array(
						'key'     => $key,
						'value'   => $value,
						'compare' => $compare,
					),
				),
				'tax_query'      => array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => $field,
						'terms'    => $terms,
						'operator' => $operator,
					),
				),
			)
		);

		wp_reset_query();
		return $query;
	}

	/**
	 * Related Posts by Tags.
	 *
	 * @access public
	 * @static
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $slug i.e. the slug of the post we're trying to relate to
	 * @return void
	 */
	public function related_posts_by_tag( $post_type, $slug ) {
		$term = term_exists( $slug, 'post_tag' );
		if ( 0 === $term || null === $term ) {
			return;
		}

		$query = new WP_Query(
			array(
				'post_type'     => $post_type,
				'no_found_rows' => true,
				'post_status'   => array( 'publish' ),
				'tag'           => $slug,
				'orderby'       => 'date',
				'order'         => 'DESC',
			)
		);
		wp_reset_query();
		return $query;
	}

}

new LWTV_Loops();
