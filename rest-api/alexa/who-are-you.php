<?php
/*
Description: REST-API - Alexa Skills - Who Are You

This is how we figure out who the fuck an actor is

Version: 1.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

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
	public function actor( $name = false ) {

		$failure = 'I\'m sorry, I don\'t recognize that name. Please try again, asking me who a specific actor is.';
		if ( ! $name ) {
			return $failure;
		}

		// Get the actor array:
		$results = self::search_this( 'actors', $name );

		if ( ! isset( $results ) || ! $results ) {
			$output = 'I can\'t find an actor who has played a queer character by that name.';
		} else {
			if ( count( $results ) > 1 ) {
				$output = 'I found more than one actor matching that name. ';
			}

			foreach ( $results as $actor ) {

				if ( false !== $actor['start'] ) {
					$age_stuff = 'was born ' . $actor['start'];
					if ( false !== $actor['end'] ) {
						$age_stuff .= ' and';
					}
				}

				if ( false !== $actor['end'] ) {
					$age_stuff .= ' died ' . $actor['end'];

					if ( false !== $actor['age'] ) {
						$age_stuff .= ' at ' . $actor['age'] . ' years of age.';
					}
				} else {
					if ( false !== $actor['age'] ) {
						$age_stuff .= ' is ' . $actor['age'] . ' years old.';
					}
				}

				// translators: %s is the number of queer characters
				$characters = ( 0 === $actor['characters'] ) ? 'no queer characters' : sprintf( _n( '%s queer character', '%s queer characters', $actor['characters'] ), $actor['characters'] );

				// The output
				$output .= $actor['name'] . ' is a ' . $actor['gender'] . ' ' . $actor['sexuality'] . ' who has played ' . $characters . ' on television. ' . $actor['name'] . ' ' . $age_stuff . '.';

				if ( '' !== $actor['content'] && strlen( $actor['content'] ) < 5 ) {
					$output .= $actor['content'];
				}

				// TO DO: What shows?
				// Need to list out what shows the person is on:
				// if end date == current OR This year: You can see them as X on Y.
				// ELSE: They played X on Y, X2 on Y2, and so on.
			}

			// followup: What shows has X been on?
		}

		return $output;
	}

	public function show( $name = false ) {
		$failure = 'I\'m sorry, I don\'t recognize that TV Show.';
		if ( ! $name ) {
			return $failure;
		}

		// Get the show array:
		$results = self::search_this( 'shows', $name );

		if ( ! isset( $results ) || ! $results ) {
			$output = 'I can\'t find a TV show by that name. Sometimes I have trouble with international TV shows, as IMdB may use the English name.';
		} else {
			if ( count( $results ) > 1 ) {
				$output = 'I found more than one TV show by that name. ';
			}

			foreach ( $results as $show ) {

				if ( 'current' === $show['airdates']['end'] ) {
					$airs = 'has been on the air since ' . $show['airdates']['start'];
				} else {
					$airs = 'aired from ' . $show['airdates']['start'] . ' to ' . $show['airdates']['end'];
				}

				// translators: %s is the number of queer characters
				$characters = ( 0 === $show['characters'] ) ? 'zero named queer characters' : sprintf( _n( '%s queer character', '%s queer characters', $show['characters'] ), $show['characters'] );

				// Output. It's basic.
				$output .= 'What can I tell you about ' . $show['name'] . '? ' . $show['content'] . ' ' . $airs . ' on ' . $show['stations'] . ' in ' . $show['nations'] . '. A total of ' . $characters . ' have been on the show.';
			}

		}

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
		if ( ! $name ) {
			return $failure;
		}

		// Get the actor array:
		$results = self::search_this( 'actors', $name );

		if ( isset( $results ) ) {
			if ( count( $results ) > 1 ) {
				$output = 'I found more than one actor matching that name. ';
			}

			foreach ( $results as $actor ) {

				$output .= $actor['name'] . ' is a ' . strtolower( $actor['gender'] ) . ' and identifies as ' . strtolower( $actor['sexuality'] ) . '.';

				$output .= 'Would you like to learn more about them? Ask me "Tell me about the actor ' . $actor['name'] . '".';
			}
		} else {
			$output = 'I can\'t find an actor who has played a character by that name.';
		}

		return $output;
	}

	/**
	 * search_actors function.
	 *
	 * @access public
	 * @param mixed $name (default: = false)
	 * @return void
	 */
	public function search_this( $posttype, $name = false ) {

		// If there's no name or it's not a valid post type, bail.
		if ( ! $name || ! in_array( $posttype, array( 'actors', 'characters', 'shows' ), true ) ) {
			return false;
		}

		// Remove <!--fwp-loop--> from output
		// phpcs:ignore
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

		$args = array(
			's'              => $name,
			'post_type'      => 'post_type_' . $posttype,
			'post_status'    => 'publish',
			'posts_per_page' => 5,
		);

		$the_this   = new WP_Query( $args );
		$this_array = array();

		if ( $the_this->have_posts() ) {

			while ( $the_this->have_posts() ) {

				$the_this->the_post();

				// Check display name...
				// If it matches, we'll go
				$title_array = explode( ' ', get_the_title() );
				$short_name  = current( $title_array ) . end( $title_array );

				if ( strtolower( get_the_title() ) === strtolower( $name ) || strtolower( $short_name ) === strtolower( $name ) ) {

					$post_name = get_post_field( 'post_name' );

					$this_array[ $post_name ] = array(
						'name'    => get_the_title()
					);

					switch ( $posttype ) {
						case 'actors':
							// Figure out the age
							// Use it like: $age->format( '%Y years old' );
							$end   = ( get_post_meta( get_the_ID(), 'lezactors_death', true ) ) ? new DateTime( get_post_meta( get_the_ID(), 'lezactors_death', true ) ) : new DateTime();
							$start = ( get_post_meta( get_the_ID(), 'lezactors_birth', true ) ) ? new DateTime( get_post_meta( get_the_ID(), 'lezactors_birth', true ) ) : false;
							$age   = false;
							if ( false !== $start ) {
								$age = $start->diff( $end );
							}

							$gender       = array();
							$sexuality    = array();
							$gender_terms = get_the_terms( get_the_ID(), 'lez_actor_gender', true );
							if ( $gender_terms && ! is_wp_error( $gender_terms ) ) {
								foreach ( $gender_terms as $gender_term ) {
									$gender[] = $gender_term->name;
								}
							}
							$sexuality_terms = get_the_terms( get_the_ID(), 'lez_actor_sexuality', true );
							if ( $sexuality_terms && ! is_wp_error( $sexuality_terms ) ) {
								foreach ( $sexuality_terms as $sexuality_term ) {
									$sexuality[] = $sexuality_term->name;
								}
							}

							// Custom add on for Actors
							$this_array[ $post_name ]['content']    = apply_filters( 'the_content', get_the_content() );
							$this_array[ $post_name ]['characters'] = get_post_meta( get_the_ID(), 'lezactors_char_count', true );
							$this_array[ $post_name ]['gender']     = implode( ', ', $gender );
							$this_array[ $post_name ]['sexuality']  = implode( ', ', $sexuality );
							$this_array[ $post_name ]['born']       = $start;
							$this_array[ $post_name ]['died']       = $end;
							$this_array[ $post_name ]['age']        = $age;
							break;
						case 'characters':
							// Custom output for characters? Alive or dead? Played by?
							break;
						case 'shows':

							if ( get_post_meta( get_the_ID(), 'lezshows_airdates', true ) ) {
								$airdates = get_post_meta( get_the_ID(), 'lezshows_airdates', true );

								// If the start is 'current' make it this year (though it really never should be.)
								if ( 'current' === $airdates['start'] ) {
									$airdates['start'] = date( 'Y' );
								}
							}

							$nation_terms = get_the_terms( get_the_ID(), 'lezshows_tvnations', true );
							if ( $nation_terms && ! is_wp_error( $nation_terms ) ) {
								foreach ( $nation_terms as $nation_term ) {
									$nation[] = $nation_term->name;
								}
							}
							$last_nation = array_pop( $nation );
							array_push( $nation, 'and ' . $last_nation);

							$station_terms = get_the_terms( get_the_ID(), 'lezshows_tvstations', true );
							if ( $station_terms && ! is_wp_error( $station_terms ) ) {
								foreach ( $station_terms as $station_term ) {
									$station[] = $station_term->name;
								}
							}
							$last_station = array_pop( $station );
							array_push( $station, 'and ' . $last_station);

							$characters = ( get_post_meta( get_the_ID(), 'lezshows_char_count', true ) ) ? get_the_ID(), 'lezshows_char_count', true ) : 0;

							$this_array[ $post_name ]['characters'] = $characters;
							$this_array[ $post_name ]['$airdates']  = $airdates;
							$this_array[ $post_name ]['nations']    = implode( ', ', $nation );
							$this_array[ $post_name ]['stations']   = implode( ', ', $station );
							$this_array[ $post_name ]['content']    = wp_strip_all_tags( get_the_excerpt(), true );
							break;
					}
				}
			}
			wp_reset_postdata();
		}

		if ( ! isset( $this_array ) ) {
			return false;
		}

		return $actor_arr;
	}

}

new LWTV_Alexa_Who();
