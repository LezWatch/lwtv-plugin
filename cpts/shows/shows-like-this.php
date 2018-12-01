<?php
/**
 * Name: Shows Like This
 * Description: Calculate other shows you'd like if you like this
 * This requires https://wordpress.org/plugins/related-posts-by-taxonomy/
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Shows_Like_This
 *
 * @since 1.0
 */

class LWTV_Shows_Like_This {

	public function __construct() {
		//add_filter( 'related_posts_by_taxonomy_posts_where', array( $this, 'where' ), 10, 4 );
		//add_filter( 'related_posts_by_taxonomy_posts_join', array( $this, 'join' ), 10, 4 );
		add_filter( 'related_posts_by_taxonomy_shortcode_defaults', array( $this, 'defaults' ) );
	}

	public static function defaults( $defaults ) {
		$defaults['post_id']          = '';
		$defaults['taxonomies']       = 'all';
		$defaults['post_types']       = '';
		$defaults['posts_per_page']   = 5;
		$defaults['order']            = 'DESC';
		$defaults['orderby']          = 'post_date';
		$defaults['before_shortcode'] = '<div class="rpbt_shortcode">'; // Text or Html
		$defaults['after_shortcode']  = '</div>';     // Text or Html
		$defaults['title']            = __( 'Related Posts', 'related-posts-by-taxonomy' );
		$defaults['before_title']     = '<h3>';       // Text or Html
		$defaults['after_title']      = '</h3>';      // Text or Html
		$defaults['exclude_terms']    = '';
		$defaults['include_terms']    = '';
		$defaults['related']          = true;
		$defaults['exclude_posts']    = '';
		$defaults['format']           = 'links';
		$defaults['image_size']       = 'thumbnail';  // image size
		$defaults['columns']          = 3;
		$defaults['caption']          = 'post_title';
		$defaults['link_caption']     = false;        // Don't wrap the caption in a link
		$defaults['limit_posts']      = -1;           // -1 is no limit
		$defaults['limit_year']       = '';           // number.
		$defaults['limit_month']      = '';           // number
		$defaults['public_only']      = true;        // bool. true or false.
		$defaults['include_self']     = false;        // bool. true or false.
		$defaults['post_class']       = '';           // classname. Default no class
		return $defaults;
	}

	public static function queery( $post_id, $what = 'where' ) {

		// Collect extras
		$thumb = ( get_post_meta( $post_id, 'lezshows_worthit_rating', true ) ) ? get_post_meta( $post_id, 'lezshows_worthit_rating', true ) : 'TBD';
		$star  = ( get_post_meta( $post_id, 'lezshows_stars', true ) ) ? 'EXISTS' : 'NOT EXISTS';
		$score = ( get_post_meta( $post_id, 'lezshows_the_score', true ) ) ? get_post_meta( $post_id, 'lezshows_the_score', true ) : 10;

		$meta_query = array(
			// If it has ANY star
			array(
				'key'     => 'lezshows_stars',
				'compare' => $star,
			),
			// If it's worth it
			array(
				'key'     => 'lezshows_worthit_rating',
				'value'   => $thumb,
				'compare' => '=',
			),
			// If the score is similar +/- 10
			array(
				'key'     => 'lezshows_the_score',
				'value'   => array( ( $score - 10 ), ( $score + 10 ) ),
				'type'    => 'numeric',
				'compare' => 'BETWEEN',
			),
		);

		// Array ( [join] => LEFT JOIN wp_postmeta ON (wp_posts.ID = wp_postmeta.post_id AND wp_postmeta.meta_key = 'lezshows_stars' ) LEFT JOIN wp_postmeta AS mt1 ON ( wp_posts.ID = mt1.post_id ) LEFT JOIN wp_postmeta AS mt2 ON ( wp_posts.ID = mt2.post_id ) [where] => AND ( wp_postmeta.post_id IS NULL AND ( mt1.meta_key = 'lezshows_worthit_rating' AND mt1.meta_value = 'Meh' ) AND ( mt2.meta_key = 'lezshows_the_score' AND CAST(mt2.meta_value AS SIGNED) BETWEEN '31.25' AND '51.25' ) ) )

		global $wpdb;
		$meta_sql = get_meta_sql( $meta_query, 'post', $wpdb->posts, 'ID' );

		switch ( $what ) {
			case 'join':
				$return = $meta_sql['join'];
				break;
			case 'where':
				$return = $meta_sql['where'];
				break;
			default:
				$return = $meta_sql['where'];
				break;
		}

		return $return;
	}

	public static function join( $join_sql, $post_id ) {

		// Bail if not a show.
		if ( 'post_type_shows' !== get_post_type( $post_id ) ) {
			return;
		}

		$meta_sql = self::queery( $post_id, 'join' );

		return $meta_sql;
	}

	public static function where( $where_sql, $post_id ) {

		// Bail if not a show.
		if ( 'post_type_shows' !== get_post_type( $post_id ) ) {
			return;
		}

		$meta_sql = self::queery( $post_id, 'where' );

		return $meta_sql;
	}

}

new LWTV_Shows_Like_This();
