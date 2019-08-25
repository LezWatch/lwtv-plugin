<?php
/*
Description: REST-API - Alexa Skills - Similar Shows

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

		$failure = 'I\'m sorry, I don\'t recognize that show. Please try again, asking me about a specific telelvision show.';
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
		// phpcs:ignore
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

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

}

new LWTV_Alexa_Shows();
