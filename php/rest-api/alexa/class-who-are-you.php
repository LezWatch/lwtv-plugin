<?php
/**
 * REST-API - Alexa Skills - Who Are You
 *
 * Helps find who actors, characters, and shows are.
 */

namespace LWTV\Rest_API\Alexa;

// Include common code
use LWTV\Rest_API\Alexa\Common;

/**
 * class LWTV_Alexa_Who
 */
class Who_Are_You {

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

		if ( ! isset( $results ) || ! $results || ! is_array( $results ) ) {
			$output = 'I\'m sorry, I can\'t find anyone listed by the name ' . $name . '.';
		} else {
			if ( count( $results ) > 1 ) {
				$output = 'I found more than one actor matching that name. ';
			}

			foreach ( $results as $actor ) {

				$age_stuff = '';

				// Born
				if ( false !== $actor['born'] ) {
					$age_stuff .= 'was born ' . $actor['born'];
					if ( false !== $actor['died'] ) {
						$age_stuff .= ' and';
					}
				}

				// Died
				if ( false !== $actor['died'] ) {
					$age_stuff .= ' died ' . $actor['died'];

					if ( false !== $actor['age'] ) {
						$age_stuff .= ' at ' . $actor['age'] . ' years of age.';
					}
				}

				// Is Age.
				if ( false === $actor['died'] && false !== $actor['age'] ) {
					$age_stuff .= ' is ' . $actor['age'];
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

		if ( ! isset( $results ) || ! $results || ! is_array( $results ) ) {
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
				$output .= 'What can I tell you about ' . $show['name'] . '? ' . $show['content'] . ' ' . $show['name'] . ' ' . $airs . $where . $characters . ' ';
			}
		}

		return $output;
	}

	/**
	 * Who is NAME? Let's find out...
	 *
	 * @access public
	 * @return string
	 */
	public function character( $name = false ) {

		$output = 'I\'m sorry, I don\'t recognize that name. Please try again, asking me who a specific character is.';
		if ( ! $name ) {
			return $output;
		}

		// Get the actor array:
		$results = self::search_this( 'characters', $name );

		if ( ! isset( $results ) || ! $results || ! is_array( $results ) ) {
			$output = 'I\'m sorry, I can\'t find anyone listed by the name ' . $name . '.';
		} else {
			if ( count( $results ) > 1 ) {
				$output = 'I found more than one character matching that name. ';
			}

			foreach ( $results as $character ) {

				if ( '' !== $character['content'] && strlen( $character['content'] ) > 5 ) {
					$output .= ' ' . wp_filter_nohtml_kses( $character['content'] );
				}
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

		if ( isset( $results ) && is_array( $results ) ) {
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
	public function search_this( $post_type, $name = false ) {
		// If there's no name or it's not a valid post type, bail.
		if ( ! $name || ! in_array( $post_type, array( 'actors', 'characters', 'shows' ), true ) ) {
			return false;
		}

		$queery_args  = ( new Common() )->search_posts( $post_type, $name );
		$this_search  = new \WP_Query( $queery_args );
		$return_array = array();

		if ( $this_search->have_posts() ) {
			$search_array = array();
			while ( $this_search->have_posts() ) {
				$this_search->the_post();
				switch ( $post_type ) {
					case 'actors':
						$search_array = self::search_actors( get_the_ID() );
						// We also need the content which is easier to get here.
						$search_array['content'] = apply_filters( 'the_content', get_the_content() );
						break;
					case 'characters':
						$search_array = self::search_characters( get_the_ID() );
						// We also need the content which is easier to get here.
						$search_array['content'] = apply_filters( 'the_content', get_the_content() );
						break;
					case 'shows':
						$search_array = self::search_shows( get_the_ID() );
						// We also need the content which is easier to get here.
						$search_array['content'] = wp_strip_all_tags( get_the_excerpt(), true );
						break;
				}
				$search_array['name']         = get_the_title();
				$return_array[ get_the_ID() ] = $search_array;
			}
			wp_reset_postdata();
		}

		// If the array is not set OR empty, it's invalid.
		if ( ! isset( $return_array ) || empty( $return_array ) ) {
			return false;
		}

		return $return_array;
	}

	/**
	 * Generate actor information
	 * @param  int    $post_id Post ID
	 * @return array           Actor Data
	 */
	public function search_actors( $post_id ) {

		// Age calculations
		if ( get_post_meta( $post_id, 'lezactors_death', true ) ) {
			$death = new \DateTime( get_post_meta( $post_id, 'lezactors_death', true ) );
			$end   = new \DateTime( get_post_meta( $post_id, 'lezactors_death', true ) );
		} else {
			$death = false;
			$end   = new \DateTime();
		}

		$start = ( get_post_meta( get_the_ID(), 'lezactors_birth', true ) ) ? new \DateTime( get_post_meta( $post_id, 'lezactors_birth', true ) ) : false;

		// If we have a birthdate, let's parse.
		if ( isset( $start ) && false !== $start ) {
			$age_is = $start->diff( $end );
			$born   = $start->format( 'm F, Y' );
			$age    = $age_is->format( '%Y years old' );
		}

		// If we have a death date, we'll use it.
		if ( isset( $death ) && false !== $death ) {
			$died = $start->format( 'm F, Y' );
		} else {
			$end = false;
		}

		// Gender and sexuality
		$gender       = array();
		$sexuality    = array();
		$gender_terms = get_the_terms( $post_id, 'lez_actor_gender', true );
		if ( $gender_terms && ! is_wp_error( $gender_terms ) ) {
			foreach ( $gender_terms as $gender_term ) {
				$gender[] = $gender_term->name;
			}
		}
		$sexuality_terms = get_the_terms( $post_id, 'lez_actor_sexuality', true );
		if ( $sexuality_terms && ! is_wp_error( $sexuality_terms ) ) {
			foreach ( $sexuality_terms as $sexuality_term ) {
				$sexuality[] = $sexuality_term->name;
			}
		}

		// Create data for actors
		$search_array['characters'] = get_post_meta( $post_id, 'lezactors_char_count', true );
		$search_array['gender']     = implode( ', ', $gender );
		$search_array['sexuality']  = implode( ', ', $sexuality );
		$search_array['born']       = $born;
		$search_array['died']       = $died;
		$search_array['age']        = $age;

		// Return the array
		return $search_array;
	}

	/**
	 * Generate character information
	 * @param  int    $post_id Post ID
	 * @return array           Character Data
	 */
	public function search_characters( $post_id ) {

		// Are the dead?
		$dead = ( has_term( 'dead', 'lez_cliches', $post_id ) ) ? true : false;

		// If they're dead, we need to get WHEN they died
		// (we'll only use the last one, thanks Sara Lance)
		if ( $dead && get_post_meta( $post_id, 'lezchars_death_year', true ) ) {
			$character_death = get_post_meta( $post_id, 'lezchars_death_year', true );
			if ( ! is_array( $character_death ) ) {
				$character_death = array( get_post_meta( $post_id, 'lezchars_death_year', true ) );
			}
			$rip = array();

			foreach ( $character_death as $death ) {
				if ( '/' !== substr( $death, 2, 1 ) ) {
					$date = date_format( date_create_from_format( 'Y-m-d', $death ), 'd F Y' );
				} else {
					$date = date_format( date_create_from_format( 'm/d/Y', $death ), 'F d, Y' );
				}
				$rip[] = $date;
			}
		}

		// Gender and sexuality
		$gender       = array();
		$sexuality    = array();
		$gender_terms = get_the_terms( $post_id, 'lez_gender', true );
		if ( $gender_terms && ! is_wp_error( $gender_terms ) ) {
			foreach ( $gender_terms as $gender_term ) {
				$gender[] = $gender_term->name;
			}
		}
		$sexuality_terms = get_the_terms( $post_id, 'lez_sexuality', true );
		if ( $sexuality_terms && ! is_wp_error( $sexuality_terms ) ) {
			foreach ( $sexuality_terms as $sexuality_term ) {
				$sexuality[] = $sexuality_term->name;
			}
		}

		// Create data for actors
		$search_array['gender']     = implode( ', ', $gender );
		$search_array['sexuality']  = implode( ', ', $sexuality );
		$search_array['dead']       = $dead;
		$search_array['death_date'] = $rip; // This is an array

		// Return the array
		return $search_array;
	}

	/**
	 * Generate show information
	 * @param  int    $post_id Post ID
	 * @return array           Show Data
	 */
	public function search_shows( $post_id ) {

		if ( get_post_meta( $post_id, 'lezshows_airdates', true ) ) {
			$airdates = get_post_meta( $post_id, 'lezshows_airdates', true );

			// If the start is 'current' make it this year (though it really never should be.)
			if ( 'current' === $airdates['start'] ) {
				$airdates['start'] = gmdate( 'Y' );
			}
		}

		$nation       = array();
		$nation_terms = get_the_terms( $post_id, 'lez_country', true );
		if ( $nation_terms && ! is_wp_error( $nation_terms ) ) {
			foreach ( $nation_terms as $nation_term ) {
				$nation[] = $nation_term->name;
			}
		}
		if ( is_array( $nation ) && ! empty( $nation ) ) {
			$last_nation = array_pop( $nation );
			if ( count( $nation ) > 1 ) {
				array_push( $nation, 'and ' . $last_nation );
				$nation = implode( ', ', $nation );
			} else {
				$nation = $last_nation;
			}
		}

		$station       = array();
		$station_terms = get_the_terms( $post_id, 'lez_stations', true );
		if ( $station_terms && ! is_wp_error( $station_terms ) ) {
			foreach ( $station_terms as $station_term ) {
				$station[] = $station_term->name;
			}
		}
		if ( is_array( $station ) && ! empty( $station ) ) {
			$last_station = array_pop( $station );
			if ( count( $station ) > 1 ) {
				array_push( $station, 'and ' . $last_station );
				$station = implode( ', ', $station );
			} else {
				$station = $last_station;
			}
		}

		$characters = ( get_post_meta( $post_id, 'lezshows_char_count', true ) ) ? get_post_meta( $post_id, 'lezshows_char_count', true ) : 0;

		$search_array['characters'] = $characters;
		$search_array['airdates']   = $airdates;
		$search_array['stations']   = $station;
		$search_array['nations']    = $nation;

		// Return the array
		return $search_array;
	}
}
