<?php
/*
Description: REST-API - Alexa Skills - Who Are You

This is how we figure out who the fuck an actor is

Version: 1.0
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Alexa_Newest
 */
class LWTV_Alexa_Who {

	/**
	 * Who is NAME? Let's find out...
	 * 
	 * @access public
	 * @return string
	 */
	public function who_is( $name = false ) {

		$failure = 'I\'m sorry, I don\'t recognize that name. Please try again, asking me who a specific actor is.';
		if ( !$name ) return $failure;

		// Get the actor array:
		$results = self::search_this( $name );

		if ( isset( $results ) ) {

			if ( count ( $results ) > 1 ) {
				$output = 'I found more than one actor matching that name. ';
			}

			foreach ( $results as $actor ) {
				$queer      = ( $actor[ 'is_queer' ] )? 'a queer actor' : 'an actor';
				$characters = ( $actor[ 'characters' ] == 0 )? 'no characters' : sprintf( _n( '%s character', '%s characters', $actor[ 'characters' ] ), $actor[ 'characters' ] );
				$output .= $actor[ 'name' ] . ' is ' . $queer . ' who has played ' . $characters . ' on television.';
			}

		} else {
			$output = 'I can\'t find an actor who has played a character by that name.';
		}

		return $output;
	}


	/**
	 * is_gay function.
	 * 
	 * @access public
	 * @param bool $name (default: false)
	 * @return void
	 */
	public function is_gay( $name = false ) {

		$failure = 'I\'m sorry, I don\'t recognize that name. Please try again, asking me who a specific actor is.';
		if ( !$name ) return $failure;

		// Get the actor array:
		$results = self::search_this( $name );

		if ( isset( $results ) ) {
			if ( count ( $results ) > 1 ) {
				$output = 'I found more than one actor matching that name. ';
			}

			foreach ( $results as $actor ) {
				$queer      = ( $actor[ 'is_queer' ] )? 'is queer' : 'is not queer';
				
				switch ( $actor[ 'gender' ] ) {
					case 'Cis Woman':
					case 'Trans Woman':
						$pronoun = 'She identifies';
						break;
					case 'Cis Man':
					case 'Trans Man':
						$pronoun = 'He identifies';
						break;
					default:
						$pronoun = 'They identify';
				}
				
				
				$output .= $actor[ 'name' ] . ' ' . $queer . '. ' . $pronoun . ' as a ' . strtolower( $actor[ 'sexuality' ] ) . ' ' . strtolower( $actor[ 'gender' ] ) . '.';
			}
		} else {
			$output = 'I can\'t find an actor who has played a character by that name.';
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

		if ( !$name ) return false;

		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

/*
		// Force to search ONLY by title
		add_filter( 'posts_search', function( $search, &$wp_query ) {
			global $wpdb;
			if ( empty( $search ) )
				return $search; // skip processing - no search term in query

			$q = $wp_query->query_vars;
			$n = ! empty( $q['exact'] ) ? '' : '%';
			$search = 
			$searchand = '';
			foreach ( (array) $q['search_terms'] as $term ) {
				$term = esc_sql( like_escape( $term ) );
				$search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
				$searchand = ' AND ';
			}
			if ( ! empty( $search ) ) {
				$search = " AND ({$search}) ";
				if ( ! is_user_logged_in() )
					$search .= " AND ($wpdb->posts.post_password = '') ";
			}
			return $search;
		} , 500, 2 );
*/
		$args = array(
			's'              => $name,
			'post_type'      => 'post_type_actors',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
		);

		$the_actor = new WP_Query( $args );
		$actor_arr = array();

		if ( $the_actor->have_posts() ) {

			while ( $the_actor->have_posts() ) {

				$the_actor->the_post();

				// Figure out the age
				// Use it like: $age->format( '%Y years old' );
				$end   = ( get_post_meta( get_the_ID(), 'lezactors_death', true ) )? new DateTime( get_post_meta( get_the_ID(), 'lezactors_death', true ) ) : new DateTime() ;
				$start = ( get_post_meta( get_the_ID(), 'lezactors_birth', true ) )? new DateTime( get_post_meta( get_the_ID(), 'lezactors_birth', true ) ) : false;
				$age   = false;
				if ( $start !== false ) {
					$age = $start->diff( $end );
				}

				$gender = $sexuality = array();
				$gender_terms = get_the_terms( get_the_ID(), 'lez_actor_gender', true );
				if ( $gender_terms && ! is_wp_error( $gender_terms ) ) {
					foreach( $gender_terms as $gender_term ) {
						$gender[] = $gender_term->name;
					}
				}
				$sexuality_terms = get_the_terms( $the_ID, 'lez_actor_sexuality', true );
				if ( $sexuality_terms && ! is_wp_error( $sexuality_terms ) ) {
					foreach( $sexuality_terms as $sexuality_term ) {
						$sexuality[] = $sexuality_term->name;
					}
				}

				$actor_arr[ get_post_field( 'post_name' ) ] = array(
					'name'       => get_the_title(),
					'characters' => get_post_meta( get_the_ID(), 'lezactors_char_count', true ),
					'dead'       => get_post_meta( get_the_ID(), 'lezactors_dead_count', true ),
					'is_queer'   => get_post_meta( get_the_ID(), 'lezactors_queer', true ),
					'gender'     => implode( ', ', $gender ),
					'sexuality'  => implode( ', ', $sexuality ),
					'age'        => $age,
				);

			}
			wp_reset_postdata();
		}

		return $actor_arr;
	}

}

new LWTV_Alexa_Who();