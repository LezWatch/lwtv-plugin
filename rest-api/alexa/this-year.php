<?php
/*
Description: REST-API - Alexa Skills - This Year

Gives you an idea how this year is going...

Version: 1.0
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Alexa_This_Year
 */
class LWTV_Alexa_This_Year {


	/**
	 * what_happened function.
	 * 
	 * @access public
	 * @param bool $date (default: false)
	 * @return void
	 */
	public function what_happened( $date = false ) {

		$date        = ( $date == false )? date('Y') : $date;
		$today       = ( $date !== date( 'Y-m-d' ) )? false : true;
		$count_array = array();

		// Figure out what date we're working with here...
		if ( preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date ) ) {
			$format   = 'day';
			$datetime = DateTime::createFromFormat( 'Y-m-d', $date );
		}
		if ( preg_match( '/^[0-9]{4}-[0-9]{2}$/', $date ) ) {
			$format   = 'month';
			$datetime = DateTime::createFromFormat( 'Y-m', $date );
		}
		if ( preg_match( '/^[0-9]{4}$/', $date ) ) {
			$format   = 'year';
			$datetime = DateTime::createFromFormat( 'Y', $date );
		}
		
		// If it's the future, be smarter than Alexa...
		if ( $datetime->format( 'Y' ) > date( 'Y' ) ) {
			$datetime->modify('-1 year');
		}

		// Calculate death
		switch ( $format ) {
			case 'year':
				$death_query         = LWTV_Loops::post_meta_and_tax_query( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'Y' ), 'lez_cliches', 'slug', 'dead', 'REGEXP' );
				$count_array['dead'] = $death_query->post_count;
				break;
			case 'month':
				$death_query         = LWTV_Loops::post_meta_and_tax_query( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'Y' ), 'lez_cliches', 'slug', 'dead', 'REGEXP' );
				$death_list_array    = LWTV_BYQ_JSON::list_of_dead_characters( $death_query );
				$death_query_count   = 0;
				foreach ( $death_list_array as $the_dead ) {
					if ( $datetime->format( 'm' ) == date( 'm' , $the_dead['died'] ) ) {
						$death_query_count++;
					}
				}
				$count_array['dead'] = $death_query_count;
				break;
			case 'day':
				$death_query         = LWTV_Loops::post_meta_and_tax_query( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'm/d/Y' ), 'lez_cliches', 'slug', 'dead', 'REGEXP' );
				$count_array['dead'] = $death_query->post_count;
				break;
			default:
				$count_array['dead'] = 0;
		}
		$dead = 'Miraculously, no characters died!';
			if ( $count_array['dead'] > 0 ) {
				$dead = sprintf( _n( '%s character died', '%s characters died', $count_array['dead'] ), $count_array['dead'] );
			}
		// Now to personalize it...
		if ( $count_array['dead'] > 20 ) {
			$dead = 'Disturbingly, ' . $dead;
		} elseif ( $count_array['dead'] > 30 ) {
			$dead = 'Distressingly, ' . $dead;
		} elseif ( $count_array['dead'] > 40 ) {
			$dead = 'Horrifyingly, ' . $dead;
		}

		// Calculate characters and shows
		$valid_post_types = array( 
			'posts'      => 'post',
			'shows'      => 'post_type_shows',
			'characters' => 'post_type_characters',
			'actors'     => 'post_type_actors',
		);

		switch ( $format ) {
			case 'day':
				$date_args = array( 'year' => $datetime->format( 'Y' ), 'month' => $datetime->format( 'm' ), 'day' => $datetime->format( 'd' ) );
				break;
			case 'month':
				$date_args = array( 'year' => $datetime->format( 'Y' ), 'month' => $datetime->format( 'm' ) );
				break;
			default:
				$date_args = array( 'year' => $datetime->format( 'Y' ) );
				break;
		}

		foreach( $valid_post_types as $name => $type ) {
			$post_args = array(
				'post_type'      => $type,
				'posts_per_page' => '-1', 
				'orderby'        => 'date', 
				'order'          => 'DESC',
				'date_query' => array( $date_args ),
			);
			$queery = new WP_Query( $post_args );
			
			$count_array[$name] = $queery->post_count;
			wp_reset_postdata();
		}
		
		$characters = ( $count_array['characters'] == 0 )? 'no characters' : sprintf( _n( '%s character', '%s characters', $count_array['characters'] ), $count_array['characters'] );
		$shows      = ( $count_array['shows'] == 0 )? 'no shows' : sprintf( _n( '%s show', '%s shows', $count_array['shows'] ), $count_array['shows'] );
		$posts      = ( $count_array['posts'] == 0 )? 'no posts' : sprintf( _n( '%s post', '%s posts', $count_array['posts'] ), $count_array['posts'] );

		// Language sucks...
		if ( $today ) {
			$intro = 'Today ';
		} else {
			switch ( $format ) {
				case 'day':
					$intro = 'On ' . $datetime->format( 'l, F jS, Y' );
					break;
				case 'month':
					$intro = 'In ' . $datetime->format( 'F Y' );
					break;
				default:
					$intro  = ( $datetime->format( 'Y' ) == date( 'Y' ) )? 'So far, in ' : 'In ';
					$intro .= $datetime->format( 'Y' );
					break;
			}
		}
		
		// Conclusion
		if ( $datetime->format( 'Y' ) > 2013 ) {
			$output = $intro . ', Lez Watch T. V. made ' . $posts . ', added ' . $characters . ', and added ' . $shows . '. ' . $dead . '.';
		} else {
			$show_data  = self::count_shows();
			$on_the_air = ( $show_data['current'] == 0 )? 'no shows' : sprintf( _n( '%s show', '%s show', $show_data['current'] ), $count_array['current'] );
			$started    = ( $show_data['started'] == 0 )? 'no shows' : sprintf( _n( '%s show', '%s show', $show_data['started'] ), $count_array['started'] );
			$ended      = ( $show_data['ended'] == 0 )? 'no shows' : sprintf( _n( '%s show', '%s show', $show_data['ended'] ), $count_array['ended'] );

			$output = 'Looking at the history of queer female and trans characters on television, I can tell you some things about ' . $datetime->format( 'Y' ) . ' ... ' . $dead . ' Also '  . $on_the_air . ' with queer female or trans characters were on the air, while '  . $started . ' started and '  . $ended . ' ended that year.';
		}

		return $output;
	}


	/**
	 * count_shows function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	static function count_shows() {

		$shows_queery = LWTV_Loops::post_type_query( 'post_type_shows' );
		$shows_this_year = array( 'current' => 0, 'ended' => 0, 'started' => 0);

		if ( $shows_queery->have_posts() ) {
			while ( $shows_queery->have_posts() ) {
				$shows_queery->the_post();
	
				$show_id   = get_the_ID();
				$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
				$show_name = strtolower( $show_name );
	
				// Shows Currently Airing
				if ( get_post_meta( $show_id, 'lezshows_airdates', true) ) {
					$airdates = get_post_meta( $show_id, 'lezshows_airdates', true);
	
					if (
						( $airdates['finish'] == 'current' && $thisyear == date('Y') ) // Still Current and it's NOW
						|| ( $airdates['finish'] >= $thisyear && $airdates['start'] <= $thisyear ) // Airdates between
					) {
					// Currently Airing Shows shows for the current year only
						$shows_this_year['current']++;
					}
	
					// Shows that ended this year
					if( $airdates['finish'] == $thisyear ) {
						$shows_this_year['ended']++;
					}
	
					// Shows that STARTED this year
					if ( $airdates['start'] == $thisyear ) {
						$shows_this_year['started']++;
					}
				}
			}
		}
		return $shows_this_year;
	}

}

new LWTV_Alexa_This_Year();