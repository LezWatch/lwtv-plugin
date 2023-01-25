<?php
/*
Name: REST-API - Alexa Skills - Shows
Description: Calculates related shows (similar_to), recommended shows.
Tags: Alexa
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

// Include common code
require_once '_common.php';

/**
 * class LWTV_Alexa_Shows
 */
class LWTV_Alexa_Shows {

	/**
	 * Similar Shows To ...
	 * Figure out what shows are like other shows (i.e. the showbot)
	 *
	 * @access public
	 * @return string List of similar shows.
	 */
	public function similar_to( $name = false ) {

		$failure = 'I\'m sorry, I don\'t recognize that show. Please try again, asking me about a specific television show.';
		if ( ! $name ) {
			return $failure;
		}

		// Get the actor array:
		$results = self::search_this( $name );
		$output  = '';

		if ( ! isset( $results ) || empty( $results ) ) {
			$output = 'I can\'t find an television show by that name. That may mean I have no recorded characters for that show.';
		} else {
			if ( count( $results ) > 1 ) {
				$output = 'I found more than one television show matching that name. ';
			}

			foreach ( $results as $show ) {
				$show_slug = get_post_field( 'post_name', $show );
				$show_name = get_the_title( $show );
				$similar   = ( new LWTV_Shows_Like_This_JSON() )->similar_show( $show_slug );
				foreach ( $similar['related'] as $a_show ) {
					$related_array[] = $a_show['title'];
				}
				if ( isset( $related_array ) ) {
					if ( count( $related_array ) > 1 ) {
						$last_element = array_pop( $related_array );
						array_push( $related_array, 'and ' . $last_element );
					}
					$related_string = implode( ', ', $related_array );
				}

				$output .= 'If you like ' . $show_name . ' then you may also like these shows. ' . $related_string . '. ';
			}
		}

		return $output;
	}

	/**
	 * Recommended Shows
	 * This lists all the shows we love.
	 *
	 * @return string Shows we recommend
	 */
	public function recommended() {

		// Number of shows
		$count = wp_count_posts( 'post_type_shows' )->publish;

		// @codingStandardsIgnoreStart
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );
		// @codingStandardsIgnoreEnd

		$args  = array(
			'post_type'      => 'post_type_shows',
			'post_status'    => 'publish',
			'posts_per_page' => 40,
			'orderby'        => 'rand',
			'no_found_rows'  => true,
			'meta_query'     => array(
				array(
					'key'     => 'lezshows_worthit_show_we_love',
					'value'   => 'on',
					'compare' => '=',
				),
			),
		);
		$love_list  = new WP_Query( $args );
		$love_array = array();

		if ( $love_list->have_posts() ) {
			$output = 'Out of the ' . $count . ' shows in our database, we recommend the following: ';
			while ( $love_list->have_posts() ) {
				$love_list->the_post();
				$love_array[] = get_the_title();
			}
			wp_reset_postdata();
		}

		if ( isset( $love_array ) ) {
			$random_love = $love_array[ array_rand( $love_array ) ];
			if ( count( $love_array ) > 1 ) {
				$last_element = array_pop( $love_array );
				array_push( $love_array, 'and ' . $last_element );
			}
			$love_string = implode( ', ', $love_array );
			$output     .= $love_string;

			if ( isset( $random_love ) ) {
				$output .= '. Want to know more? You can ask me: "Tell me about the show "' . $random_love . '"".';
			}
		} else {
			$output = 'Something must be wrong. Our show robot cannot recommend any shows at this time. Try asking me "What shows are like Batwoman" while we get this fixed.';
		}

		return $output;
	}

	/**
	 * search_this function.
	 *
	 * @access public
	 * @param mixed $name (default: = false)
	 * @return void
	 */
	public function search_this( $name = false ) {

		if ( ! $name ) {
			return false;
		}

		$queery_args  = ( new LWTV_Alexa_Common() )->search_posts( 'shows', $name );
		$the_shows    = new WP_Query( $queery_args );
		$return_array = array();

		if ( $the_shows->have_posts() ) {
			while ( $the_shows->have_posts() ) {
				$the_shows->the_post();
				$return_array[] = get_the_ID();
			}
			wp_reset_postdata();
		}

		//error_log( print_r( $return_array, true ) );

		if ( ! isset( $return_array ) || empty( $return_array ) ) {
			return false;
		}

		return $return_array;
	}

}

new LWTV_Alexa_Shows();
