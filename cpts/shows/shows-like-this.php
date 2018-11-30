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
		//add_filter( 'related_posts_by_taxonomy_pre_related_posts', array( $this, 'override' ), 10, 4 );
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

	public static function override( $related_posts, $args ) {
		// Use widget or shortcode settings for our own query.
		$my_query_args = array(
			'post_type'      => $args['post_types'],
			'posts_per_page' => $args['posts_per_page'],
			'public_only'    => $args['public_only'],
		);

		// Collect extras
		$thumb = ( get_post_meta( $args['post_id'], 'lezshows_worthit_rating', true ) ) ? get_post_meta( $args['post_id'], 'lezshows_worthit_rating', true ) : 'TBD';
		$star  = ( get_post_meta( $args['post_id'], 'lezshows_stars', true ) ) ? 'EXISTS' : 'NOT EXISTS';
		$score = ( get_post_meta( $args['post_id'], 'lezshows_the_score', true ) ) ? get_post_meta( $args['post_id'], 'lezshows_the_score', true ) : 10;

		$meta_array = array(
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

		$my_query_args['meta_query'] = $meta_array;

		$my_related_posts = get_posts( $my_query_args );

		return $my_related_posts;
	}

}

new LWTV_Shows_Like_This();
