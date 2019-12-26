<?php
/*
Description: REST-API - Alexa Skills - Shows

This is how we figure out what shows are like other shows

Version: 1.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Alexa_Shows
 */
class LWTV_Alexa_Shows {

	/**
	 * Who is NAME? Let's find out...
	 *
	 * @access public
	 * @return string
	 */
	public function similar_to( $name = false ) {

		$failure = 'I\'m sorry, I don\'t recognize that show. Please try again, asking me about a specific television show.';
		if ( ! $name ) {
			return $failure;
		}

		// Get the actor array:
		$results = self::search_this( $name );
		$output  = '';

		if ( ! isset( $results ) || ! $results ) {
			$output = 'I can\'t find an television show by that name. That may mean I have no recorded characters for that show.';
		} else {
			if ( count( $results ) > 1 ) {
				$output = 'I found more than one television show matching that name. ';
			}

			foreach ( $results as $show ) {
				$similar = LWTV_Shows_Like_This_JSON::similar_show( $show );
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

				$output .= 'If you like ' . $show . ' then you may also like these shows. ' . $related_string . '. ';
			}
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

		// Remove <!--fwp-loop--> from output
		// @codingStandardsIgnoreStart
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );
		// @codingStandardsIgnoreEnd

		$args = array(
			's'              => $name,
			'post_type'      => 'post_type_shows',
			'post_status'    => 'publish',
			'posts_per_page' => 5,
		);

		$the_show = new WP_Query( $args );
		$show_arr = array();

		if ( $the_show->have_posts() ) {

			while ( $the_show->have_posts() ) {

				$the_show->the_post();

				// Check display name...
				// If it matches, we'll go
				$title_array = explode( ' ', get_the_title() );
				$short_name  = current( $title_array ) . end( $title_array );

				if ( strtolower( get_the_title() ) === strtolower( $name ) || strtolower( $short_name ) === strtolower( $name ) ) {
					$show_arr[] = $the_show->post_name();
				}
			}
			wp_reset_postdata();
		}

		if ( ! isset( $show_arr ) ) {
			return false;
		}

		return $show_arr;
	}

	public static function recommended() {

		// Number of shows
		$count = wp_count_posts( 'post_type_shows' )->publish;

		// @codingStandardsIgnoreStart
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );
		// @codingStandardsIgnoreEnd

		$args  = array(
			'post_type'      => 'post_type_shows',
			'post_status'    => 'publish',
			'posts_per_page' => 40,
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

}

new LWTV_Alexa_Shows();
