<?php
/*
Description: REST-API - Alexa Skills - Who Are You

This is how we figure out who the fuck an actor is

Version: 1.1
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

		$output = 'I\'m sorry, I don\'t recognize that name. Please try again, asking me who a specific actor is.';
		if ( ! $name ) {
			return $output;
		}

		// Get the actor array:
		$results = self::search_this( 'actors', $name );

		if ( ! isset( $results ) || ! $results ) {
			$output = 'I\'m sorry, I can\'t find anyone listed by the name ' . $name . '.';
		} else {
			if ( count( $results ) > 1 ) {
				$output = 'I found more than one actor matching that name. ';
			}

			foreach ( $results as $actor ) {

				if ( false !== $actor['born'] ) {
					$age_stuff = 'was born ' . $actor['born']->format( 'm F, Y' );
					if ( false !== $actor['end'] ) {
						$age_stuff .= ' and';
					}
				}

				if ( false !== $actor['died'] ) {
					$age_stuff .= ' died ' . $actor['died']->format( 'm F, Y' );

					if ( false !== $actor['age'] ) {
						$age_stuff .= ' at ' . $actor['age'] . ' years of age.';
					}
				} else {
					if ( false !== $actor['age'] ) {
						$age_stuff .= ' is ' . $actor['age'];
					}
				}

				// translators: %s is the number of queer characters
				$characters = ( 0 === $actor['characters'] ) ? 'no queer characters' : sprintf( _n( '%s queer character', '%s queer characters', $actor['characters'] ), $actor['characters'] );

				// The output
				$output = $actor['name'] . ' is a ' . strtolower( $actor['gender'] ) . ' who identifies as ' . strtolower( $actor['sexuality'] ) . ' and has played ' . $characters . ' on television. ' . $actor['name'] . ' ' . $age_stuff . '.';

				if ( '' !== $actor['content'] && strlen( $actor['content'] ) > 5 ) {
					$output .= ' ' . wp_filter_nohtml_kses( $actor['content'] );
				}
			}
		}

		return $output;
	}

	/**
	 * What is show?
	 */
	public function show( $name = false ) {
		$output = 'I\'m sorry, I don\'t recognize that television show by name.';
		if ( ! $name ) {
			return $output;
		}

		// Get the show array:
		$results = self::search_this( 'shows', $name );

		if ( ! isset( $results ) || ! $results ) {
			$output = 'I can\'t find a TV show by the name "' . ucfirst( $name ) . '". Sometimes I have trouble with international TV shows, as IMdB may use the English name.';
		} else {
			$output = 'I found some information on the television show "' . ucfirst( $name ) . '." ';
			if ( count( $results ) > 1 ) {
				$output = 'I found more than one television show with the name "' . ucfirst( $name ) . '." ';
			}

			foreach ( $results as $show ) {

				// Airdates
				if ( 'current' === $show['airdates']['finish'] ) {
					$airs = 'has been on the air since ' . $show['airdates']['start'];
				} else {
					$airs = 'aired from ' . $show['airdates']['start'] . ' to ' . $show['airdates']['finish'];
				}

				// Where
				$where = ' on ' . $show['stations'] . ' in ' . $show['nations'] . '. ';

				// Character Count
				// translators: %s is the number of queer characters
				$some_chars = sprintf( _n( 'One %s queer character has been recorded on this show.', 'A total of %s queer characters have been recorded from this show.', $show['characters'] ), $show['characters'] );
				$no_chars   = 'Even though we have this show listed, we don\'t know the name of any of the queer characters.';
				$characters = ( 0 === $show['characters'] ) ? $no_chars : $some_chars;

				// Output. It's basic because if we have too much, it barfs PHP.
				$output .= 'What can I tell you about ' . $show['name'] . '? ' . $show['content'] . ' ' . $show['name'] . ' ' . $airs . $where . $characters;
			}
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

				$output .= ' Would you like to learn more about them? Ask LezWatch T. V. Tell me about the actor ' . $actor['name'] . '.';
			}
		} else {
			$output = 'I can\'t find an actor who has played a character by that name.';
		}

		return $output;
	}

	/**
	 * Search for custom posts function.
	 *
	 * @access public
	 * @param mixed $name (default: = false)
	 * @return void
	 */
	public function search_this( $posttype, $name = false ) {
		global $wpdb;

		// If there's no name or it's not a valid post type, bail.
		if ( ! $name || ! in_array( $posttype, array( 'actors', 'characters', 'shows' ), true ) ) {
			return false;
		}

		// Remove <!--fwp-loop--> from output
		// phpcs:ignore
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

		$this_array = array();

		$args       = array(
			'title'          => $name,
			'post_type'      => 'post_type_' . $posttype,
			'post_status'    => 'publish',
			'posts_per_page' => 5,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);
		$the_this   = new WP_Query( $args );

		if ( $the_this->have_posts() ) {

			while ( $the_this->have_posts() ) {

				$the_this->the_post();

				$post_name = get_post_field( 'post_name' );
				$this_array[ $post_name ] = array( 'name' => get_the_title() );

				switch ( $posttype ) {
					case 'actors':
						// Age calculations
						if ( get_post_meta( get_the_ID(), 'lezactors_death', true ) ) {
							$died = new DateTime( get_post_meta( get_the_ID(), 'lezactors_death', true ) );
							$end  = new DateTime( get_post_meta( get_the_ID(), 'lezactors_death', true ) );
						} else {
							$died = false;
							$end  = new DateTime();
						}

						$start  = ( get_post_meta( get_the_ID(), 'lezactors_birth', true ) ) ? new DateTime( get_post_meta( get_the_ID(), 'lezactors_birth', true ) ) : false;
						if ( isset( $start ) ) {
							$age_is = $start->diff( $end );
						}
						$age = $age_is->format( '%Y years old' );

						// Gender and sexuality
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

						// Create data for actors
						$this_array[ $post_name ]['content']    = apply_filters( 'the_content', get_the_content() );
						$this_array[ $post_name ]['characters'] = get_post_meta( get_the_ID(), 'lezactors_char_count', true );
						$this_array[ $post_name ]['gender']     = implode( ', ', $gender );
						$this_array[ $post_name ]['sexuality']  = implode( ', ', $sexuality );
						$this_array[ $post_name ]['born']       = $start;
						$this_array[ $post_name ]['died']       = $died;
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

						$nation = array();
						$nation_terms = get_the_terms( get_the_ID(), 'lez_country', true );
						if ( $nation_terms && ! is_wp_error( $nation_terms ) ) {
							foreach ( $nation_terms as $nation_term ) {
								$nation[] = $nation_term->name;
							}
						}
						if ( is_array( $nation ) && ! empty( $nation ) ) {
							$last_nation = array_pop( $nation );
							array_push( $nation, 'and ' . $last_nation );
							$nation = implode( ', ', $nation );
						}

						$station = array();
						$station_terms = get_the_terms( get_the_ID(), 'lez_stations', true );
						if ( $station_terms && ! is_wp_error( $station_terms ) ) {
							foreach ( $station_terms as $station_term ) {
								$station[] = $station_term->name;
							}
						}
						if ( is_array( $station ) && ! empty( $station ) ) {
							$last_station = array_pop( $station );
							array_push( $station, 'and ' . $last_station );
							$station = implode( ', ', $station );
						}

						$characters = ( get_post_meta( get_the_ID(), 'lezshows_char_count', true ) ) ? get_post_meta( get_the_ID(), 'lezshows_char_count', true ) : 0;

						$this_array[ $post_name ]['characters'] = $characters;
						$this_array[ $post_name ]['airdates']   = $airdates;
						$this_array[ $post_name ]['stations']   = $station;
						$this_array[ $post_name ]['content']    = wp_strip_all_tags( get_the_excerpt(), true );
						break;
				}
			}
			wp_reset_postdata();
		}

		if ( ! isset( $this_array ) ) {
			return false;
		}

		return $this_array;
	}

}

new LWTV_Alexa_Who();
