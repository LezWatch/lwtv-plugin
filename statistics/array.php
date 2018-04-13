<?php
/**
 * Name: Statistics Code : Arrays
 *
 * Generates arrays
 */

class LWTV_Stats_Arrays {

	/*
	 * Statistics Taxonomy Array
	 *
	 * Generate array to parse taxonomy content
	 *
	 * @param string $post_type Post Type to be search
	 * @param string $taxonomy Taxonomy to be searched
	 * @param string $terms The terms to be matched (default empty)
	 * @param string $operator Search operator (default IN)
	 *
	 * @return array
	 */
	static function taxonomy( $post_type, $taxonomy, $terms = '', $operator = 'IN' ) {
		$array = array();

		// If no term provided, use get_terms for the taxonomy
		$taxonomies = ( $terms == '' )? get_terms( $taxonomy ) : array($terms);

		foreach ( $taxonomies as $term ) {
			$term_obj  = ( $terms !== '' )? get_term_by( 'slug', $term, $taxonomy, 'ARRAY_A' ) : '';
			$term_link = get_term_link( $term, $taxonomy );
			$term_slug = ( $terms == '' )? $term->slug : $terms;
			$term_name = ( $terms == '' )? $term->name : $term_obj['name'];
			$count_terms_query = LWTV_Loops::tax_query( $post_type, $taxonomy, 'slug', $term_slug, $operator );
			$term_count = $count_terms_query->post_count;
			$array[$term_slug] = array( 'count' => $term_count, 'name' => $term_name, 'url' => $term_link );
		}
		return $array;
	}

	/*
	 * Statistics Taxonomy Array for DEAD
	 *
	 * Generate array to parse taxonomy content for death
	 *
	 * @param string $post_type Post Type to be searched
	 * @param string $taxonomy Taxonomy to be searched
	 *
	 * @return array
	 */
	static function dead_taxonomy( $post_type, $taxonomy ) {
		$array = array();
		$taxonomies = get_terms( $taxonomy );

		foreach ( $taxonomies as $term ) {
			$query = LWTV_Loops::tax_two_query(
				$post_type,
				$taxonomy, 'slug', $term->slug,
				'lez_cliches', 'slug', 'dead'
			);

			$array[$term->slug] = array( 'count' => $query->post_count, 'name'  => $term->name, 'url' => get_term_link( $term ) );
		}
		return $array;
	}

	/*
	 * Statistics Array for DEAD by ROLE
	 *
	 * Generate array to parse content for death by character role
	 *
	 * @param string $post_type Post Type to be searched
	 * @param string $taxonomy Taxonomy to be searched
	 *
	 * @return array
	 */
	static function dead_role() {
		$array = array();
		$all_the_dead = LWTV_Loops::tax_query( 'post_type_characters', 'lez_cliches', 'slug', 'dead');
		$by_role = array( 'regular' => 0, 'guest' => 0, 'recurring' => 0 );
		$alldead = 0;

		if ( $all_the_dead->have_posts() ) {

			foreach ( $all_the_dead->posts as $dead ) {
				$all_shows = get_post_meta( $dead->ID, 'lezchars_show_group', true );
				foreach ( $all_shows as $each_show ) {
					if ( $each_show['type'] == 'regular' )   $by_role['regular']++;
					if ( $each_show['type'] == 'guest' )     $by_role['guest']++;
					if ( $each_show['type'] == 'recurring' ) $by_role['recurring']++;
				}
			}
			wp_reset_query();
		}

		$array = array (
			'regular' => array( 'count' => $by_role['regular'], 'name'  => 'Regular', 'url' => home_url( '/role/regular/' ) ),
			'guest'   => array( 'count' => $by_role['guest'], 'name'  => 'Guest', 'url' => home_url( '/role/guest/' ) ),
			'recurring' => array( 'count' => $by_role['recurring'], 'name'  => 'Recurring', 'url' => home_url( '/role/recurring/' ) ),
		);

		return $array;
	}

	/*
	 * Statistics Meta and Taxonomy Array
	 *
	 * Generate array to parse taxonomy content as it relates to post metas
	 *
	 * @param string $post_type Post Type to be search
	 * @param array $meta_array Meta terms to loop through
	 * @param string $key Post Meta Key name (i.e. lezchars_gender)
	 * @param string $taxonomy Taxonomy to restrict to (default lez_cliches)
	 * @param string $field Taxonomy to restrict to (default dead)
	 *
	 * @return array
	 */
	static function dead_meta_tax( $post_type, $meta_array, $key, $taxonomy = 'lez_cliches', $field = 'dead' ) {
		$array = array();

		foreach ( $meta_array as $value ) {
			$query = LWTV_Loops::post_meta_and_tax_query( $post_type, $key, $value, $taxonomy, 'slug', $field );
			$array[$value] = array(
				'count' => $query->post_count,
				'name'  => ucfirst($value),
				'url' => home_url( '/cliche/'.$value ),
			);
		}
		return $array;
	}

	/*
	 * Statistics Simple Meta Array
	 *
	 * Generate array to parse post meta data
	 *
	 * @param string $post_type Post Type to be search
	 * @param array $meta_array Meta terms to loop through
	 * @param string $key Post Meta Key name (i.e. lezchars_gender)
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param string $compare The type of comparison (default =)
	 *
	 * @return array
	 */
	static function meta( $post_type, $meta_array, $key, $data, $compare = '=' ) {
		$array = array();
		foreach ( $meta_array as $value ) {
			$meta_query = LWTV_Loops::post_meta_query( $post_type, $key, $value, $compare );
			$array[$value] = array( 'count' => $meta_query->post_count, 'name' => ucfirst($value), 'url' => home_url( '/'. $data .'/'. lcfirst($value) .'/' ) ) ;
		}
		return $array;
	}

	/*
	 * Yes/No arrays
	 *
	 * Generate array to parse post meta data
	 *
	 * @param string $post_type Post Type to be search
	 * @param array $meta_array Meta terms to loop through
	 * @param string $key Post Meta Key name (i.e. lezchars_gender)
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param string $count Total post count
	 *
	 * @return array
	 */
	static function yes_no( $post_type, $data, $count ) {

		$array = array(
			'no'  => array( 'count' => '0', 'name' => 'No', 'url' => '' ),
			'yes' => array( 'count' => '0', 'name' => 'Yes', 'url' => '' ),
		);

		// Define the options
		switch ( $data ) {
			case 'weloveit':
				$meta_array = array( 'on' );
				$key        = 'lezshows_worthit_show_we_love';
				$compare    = '=';
				break;
			case 'current':
				$meta_array = array( 'current', 'notcurrent' );
				$key        = 'lezshows_airdates';
				$compare    = 'REGEXP';
				break;
		}

		// Collect the data
		$meta = self::meta( $post_type, $meta_array, $key, $data, $compare );

		// Parse the data
		switch ( $data ) {
			case 'weloveit':
				$array['no']['count']  = $count - $meta['on']['count'];
				$array['yes']['count'] = $meta['on']['count'];
				$array['yes']['url']   = home_url( '/shows/?fwp_show_loved=on' );
				break;
			case 'current':
				$array['no']['count']  = $count - $meta['current']['count'];
				$array['yes']['count'] = $meta['current']['count'];
				break;
		}

		return $array;
	}


	/**
	 * Calculate stats for characters_details_shows
	 *
	 * This means 'characters based on nations' and 'characters based on channels'
	 * 
	 * @access public
	 * @static
	 * @param mixed $count
	 * @param mixed $format
	 * @return void
	 */
	static function characters_details_shows( $count, $format, $data ) {

		// Set defaults
		$array = $char_data  = array();

		// Create a massive array of all the character terms we care about...
		$valid_subtaxes = array( 'gender', 'sexuality', 'romantic' );

		// [main_term_meta]
		// [main taxonomy]_[term of main]_[metadata to parse]
		// ex: [country_all_gender]
		//     [station_abc_sexuality]
		//     [country_usa_all]
		$pieces    = explode( '_', $data);
		$data_main = $pieces[0];
		$data_term = ( isset( $pieces[1] ) )? $pieces[1] : 'all';
		$data_meta = ( isset( $pieces[2] ) && in_array( $pieces[2], array_keys( $valid_subtaxes ) ) )? $pieces[2] : 'all';

		// Get the taxonomy data:
		if ( $data_term !== 'all' ) {
			$tax_term = get_term_by( 'slug', $data_term, 'lez_' . $data_main );
			$taxonomy = array( $data_term => array(
				'name' => $tax_term->name,
				'slug' => $data_term,
			) );
		} else {
			$taxonomy = get_terms( 'lez_' . $data_main );
		}

		// Parse the taxonomy
		foreach ( $taxonomy as $the_tax ) {
			$characters = 0;
			$shows      = 0;

			$slug = ( !isset( $the_tax->slug ) )? $the_tax['slug'] : $the_tax->slug;
			$name = ( !isset( $the_tax->name ) )? $the_tax['name'] : $the_tax->name;

			// Get the posts
			$queery = LWTV_Loops::tax_query( 'post_type_shows', 'lez_' . $data_main, 'slug', $slug );

			// Process
			if ( $queery->have_posts() ) {
				foreach( $queery->posts as $show ) {
					$shows++;
					// Since everyone has a gender, we'll use that as our baseline...
					$gender = get_post_meta( $show->ID, 'lezshows_char_gender' );

					// Add the character counts
					foreach( array_shift( $gender ) as $this_gender => $count ) {
						$characters += $count;
					}

					// Get the data...
					if ( $data_meta !== 'all' ) {
						$char_data_array       = get_post_meta( $show->ID, 'lezshows_char_' . $data_meta );
						$char_data[$data_meta] = array_shift( $char_data_array );
					} elseif ( $data_meta == 'all' && $data_term !== 'all' ) {
						foreach ( $valid_subtaxes as $meta ) {
							$char_data_array  = get_post_meta( $show->ID, 'lezshows_char_' . $meta );
							$char_data[$meta] = array_shift( $char_data_array );
						}
					}
				}
				wp_reset_query();
			}

			// Determine what kind of array we need to show...
			switch( $format ) {
				case 'barchart':
					if ( $data_term !== 'all' && $data_meta !== 'all' ) {
						foreach ( $char_data as $char_name => $char_count ) {
							$array[] = array (
								'name'  => $char_name,
								'count' => $char_count,
							);
						}
					} elseif ( $data_term !== 'all' && $data_meta == 'all' ) {
						$array['shows'] = array( 'name'  => 'Shows', 'count' => $shows );
						$array['chars'] = array( 'name' => 'Characters', 'count' => $characters );
						print_r( $char_data );
						foreach ( $valid_subtaxes as $meta ) {
							foreach ( $char_data[$meta] as $char_name => $char_count ) {
								$array[$char_name] = array( 'name'  => ucfirst( $char_name ), 'count' => $char_count );
							}
						}
					} else {
						$array = self::taxonomy( 'post_type_shows', 'lez_' . $data_main );
					}
					break;
				case 'percentage':
				case 'piechart':
					if ( $data_term !== 'all' ) {
						if ( $data_meta !== 'all' ) {
							foreach ( $char_data as $char_name => $char_count ) {
								$array[] = array (
									'name'  => $char_name,
									'count' => $char_count,
								);
							}
						} else {
							$array['shows'] = array( 'count' => $shows, 'name' => 'Shows', 'url' => '#' );
							$array['chars'] = array( 'count' => $characters, 'name' => 'Characters', 'url' => '#' );
						}
					} else {
						$array = self::taxonomy( 'post_type_shows', 'lez_' . $data_main );
					}
					break;
				case 'count':
					$array = count( $taxonomy );
					break;
				case 'stackedbar':
					$array[$slug] = array(
						'name'       => $name,
						'count'      => $shows,
						'characters' => $characters,
						'dataset'    => $char_data,
					);
			}
		}

		return $array;
	}

	/*
	 * Statistics Basic death
	 *
	 * Death is insane. This is just looping a lot of things to sort
	 * out who died in what year, so we can use it by other functions
	 *
	 * @param string $subject - whatever we're working with
	 * @param string $output  - Array or Count
	 *
	 * @return array or count
	 */
	static function dead_basic( $subject, $output ) {

		switch ( $subject ) {
			case 'characters':
				$taxonomy = 'lez_cliches';
				$terms    = 'dead';
				break;
			case 'shows':
				$taxonomy = 'lez_tropes';
				$terms    = 'dead-queers';
				break;
		}
		
		$array = self::taxonomy( 'post_type_'.$subject, $taxonomy, $terms );

		switch ( $subject ) {
			case 'characters':
				$array['dead'] = array( 'count' => ( $array['dead']['count']), 'name' => 'Dead Characters', 'url' => home_url( '/cliche/dead/' ) );
				$count = $array['dead']['count'];
				break;
			case 'shows':
				$array['dead-queers'] = array( 'count' => ( $array['dead-queers']['count']), 'name' => 'Shows with Dead', 'url' => home_url( '/trope/dead-queers/' ) );
				$count = $array['dead-queers']['count'];
				break;
		}
		
		switch ( $output ) {
			case 'array':
				$return = $array;
				break;
			case 'count':
				$return = $count;
				break;
		}
		
		return $return;
	}

	/*
	 * Statistics Death By Year
	 *
	 * Death is insane. This is just looping a lot of things to sort
	 * out who died in what year, so we can use it by other functions
	 *
	 * @return array
	 */
	static function dead_year() {
		// Death by year
		$year_first = FIRST_LWTV_YEAR;
		$year_deathlist_array = array();
		foreach (range(date('Y'), $year_first) as $x) {
			$year_deathlist_array[$x] = $x;
		}

		$year_death_array = array();
		foreach ( $year_deathlist_array as $year ) {
			$year_death_query = LWTV_Loops::post_meta_and_tax_query( 'post_type_characters', 'lezchars_death_year', $year, 'lez_cliches', 'slug', 'dead', 'REGEXP' );

			$year_death_array[$year] = array(
				'name'  => $year,
				'count' => $year_death_query->post_count,
				'url'   => home_url( '/this-year/'.$year.'/')
			);
		}
		return $year_death_array;
	}

	/*
	 * Statistics Death on Shows
	 *
	 * Death is insane. This is how to figure out who died on what show.
	 * We can use it to determine how many shows have ALL dead queers, etc.
	 * It's fucked up. I'm sorry.
	 *
	 * @param string $format The format of our output
	 *
	 * @return array
	 */
	static function dead_shows( $format ) {

		// Dead Queers Query
		$dead_queers_query = LWTV_Loops::tax_query( 'post_type_characters', 'lez_cliches', 'slug', 'dead' );

		// Shows With Dead Query
		$dead_shows_query = LWTV_Loops::tax_query( 'post_type_shows', 'lez_tropes', 'slug', 'dead-queers' );

		// Shows With NO Dead Query
		$alive_shows_query = LWTV_Loops::tax_query( 'post_type_shows', 'lez_tropes', 'slug', 'dead-queers', 'NOT IN' );

		// Predef Arrays
		$noneshow_death_array = array();
		$fullshow_death_array = array();
		$someshow_death_array = array();

		// Shows with no deaths
		if ( $alive_shows_query->have_posts() ) {
			while ( $alive_shows_query->have_posts() ) {
				$alive_shows_query->the_post();
				$show_id = get_the_ID();

				$show_name = preg_replace('/\s*/', '', get_the_title( $show_id ));
				$show_name = strtolower( $show_name );

				$noneshow_death_array[$show_name] = array(
					'url'    => get_permalink( $show_id ),
					'name'   => get_the_title( $show_id ),
					'status' => get_post_status( $show_id ),
				);
			}
			wp_reset_query();
		}

		// Shows with deaths
		if ( $dead_shows_query->have_posts() ) {
			while ( $dead_shows_query->have_posts() ) {
				$dead_shows_query->the_post();
				$show_id = get_the_ID();

				$show_name = preg_replace('/\s*/', '', get_the_title( $show_id ));
				$show_name = strtolower( $show_name );

				// Loop of characters who MIGHT be in this show
				$this_show_characters_query = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );

				$fulldeathcount = '0';
				$chardeathcount = '0';

				// Begin Character query
				if ( $this_show_characters_query->have_posts() ) {
					while ( $this_show_characters_query->have_posts() ) {
						$this_show_characters_query->the_post();
						$char_id = get_the_ID();
						$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true );

						if ( $shows_array !== '' ) {
							foreach( $shows_array as $char_show ) {
								if ( $char_show['show'] == $show_id ) {
									// If the character is really in this show, +1
									$chardeathcount++;

									// If the character is dead, bump the full death count
									if ( has_term( 'dead', 'lez_cliches', $char_id ) ) $fulldeathcount++;
								}
							}
						}
					}
					wp_reset_query();
				}
				// End Character Loop

				if ( $fulldeathcount == $chardeathcount ) {
					$fullshow_death_array[$show_name] = array(
						'url'    => get_permalink( $show_id ),
						'name'   => get_the_title( $show_id ),
						'status' => get_post_status( $show_id ),
					);
				} elseif ( $fulldeathcount <= $chardeathcount ) {
					$someshow_death_array[$show_name] = array(
						'url'    => get_permalink( $show_id ),
						'name'   => get_the_title( $show_id ),
						'status' => get_post_status( $show_id ),
					);
				}

			}
			wp_reset_query();
		}

		if ( $format == 'simple' ) {
			$array = array (
				"all"  => array( 'name' => 'All queers are dead', 'count' => count( $fullshow_death_array ), 'url' => '' ),
				"some" => array( 'name' => 'Some queers are dead', 'count' => count( $someshow_death_array ), 'url' => '' ),
				"none" => array( 'name' => 'None queers are dead', 'count' => $alive_shows_query->post_count, 'url' => '' ),
			);
		}

		return $array;
	}

	/*
	 * Statistics Scores
	 *
	 * @return array
	 */
	static function scores( $post_type ) {
		$the_queery   = LWTV_Loops::post_type_query( $post_type );
		$scores_array = array();
		if ( $the_queery->have_posts() ) {
			while ( $the_queery->have_posts() ) {
				$the_queery->the_post();
				$post = get_post();
				$scores_array[ $post->ID ] = array(
					'id'    => $post->ID,
					'count' => get_post_meta( $post->ID, 'lezshows_the_score', true ),
					'url'   => get_the_permalink( $post->ID ),
				);
			}
			wp_reset_query();
		}
		
		return $scores_array;
	}

	/**
	 * Statistics: Actors and Characters
	 * 
	 * @access public
	 * @static
	 * @param string $type (default: 'chars')
	 * @return void
	 */
	static function actor_chars( $type = 'characters' ) {
		// list of people
		$all_query = LWTV_Loops::post_type_query( 'post_type_' . $type );
		$array     = array();
		if ( $all_query->have_posts() ) {
			while ( $all_query->have_posts() ) {
				$all_query->the_post();
				// The data we parse depends on the data type
				switch ( $type ) {
					case 'characters':
						$data = get_post_meta( get_the_id(), 'lezchars_actor', true );
						$name = 'actors';
						break;
					case 'actors':
						$data = get_post_meta( get_the_id(), 'lezactors_char_count', true );
						$name = 'characters';
						break;
				}
				// Now that we have the data, let's count and store
				if ( is_numeric( $data ) ) {
					$key = $data;
				} else {
					$key = count( $data );
				}

				if ( !array_key_exists( $key, $array ) && is_numeric( $key ) ) {
					$array[ $key ] = array(
						'name'  => $key . ' ' . $name ,
						'count' => '1',
						'url'   => '',
					);
				} else {
					$array[ $key ]['count']++;
				}
			}
			wp_reset_query();
		}
		
		ksort( $array );
		
		return $array;
	}

	/**
	 * Statistics Roles on Shows
	 * 
	 * @access public
	 * @static
	 * @param string $type (default: 'dead')
	 * @return void
	 */
	static function show_roles( $type = 'dead' ) {
		// List of shows
		$all_shows_query = LWTV_Loops::post_type_query( 'post_type_shows' );

		$guest_alive_array = $recurring_alive_array = $main_alive_array = array();
		$guest_dead_array = $recurring_dead_array = $main_dead_array = array();

		if ( $all_shows_query->have_posts() ) {

			while ( $all_shows_query->have_posts() ) {
				$all_shows_query->the_post();
				$show_id = get_the_id();

				$show_name = preg_replace('/\s*/', '', get_the_title( $show_id ));
				$show_name = strtolower($show_name);

				$role_loop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );

				if ( $role_loop->have_posts() ) {

					$guest = $regular = $recurring = array( 'alive' => 0, 'dead' => 0 );

					$char_id     = get_the_id();
					$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true);

					if ( $shows_array !== '' ) {

						foreach( $shows_array as $each_show ) {
							if ( $char_show['type'] == 'guest' ) {
								$guest['alive']++;
								if ( has_term( 'dead', 'lez_cliches', $char_id ) ) $guest['dead']++;
							}
							if ( $char_show['type'] == 'regular' ) {
								$regular['alive']++;
								if ( has_term( 'dead', 'lez_cliches', $char_id ) ) $regular['dead']++;
							}
							if ( $char_show['type'] == 'recurring' ) {
								$recurring['alive']++;
								if ( has_term( 'dead', 'lez_cliches', $char_id ) ) $recurring['dead']++;
							}
						}
					}

					// Make Alive Query
					if ( $regular['alive'] == '0' && $recurring['alive'] != '0' && $guest['alive'] == '0' ) {
						$recurring_alive_array[$show_name] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					if ( $regular['alive'] == '0' && $recurring['alive'] == '0' && $guest['alive'] != '0' ) {
						$guest_alive_array[$show_name] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					if ( $regular['alive'] !== '0' && $guest['alive'] == '0' && $recurring['alive'] == '0' ) {
						$main_alive_array[$show_name] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}

					// Make Dead Data
					if ( $regular['dead'] == '0' && $recurring['dead'] != '0' && $guest['dead'] == '0' ) {
						$recurring_dead_array[$show_name] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					if ( $regular['dead'] == '0' && $recurring['dead'] == '0' && $guest['dead'] != '0' ) {
						$guest_dead_array[$show_name] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					if ( $regular['dead'] !== '0' && $guest['dead'] == '0' && $recurring['dead'] == '0' ) {
						$main_dead_array[$show_name] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					wp_reset_query();
				}
			}
			wp_reset_query();
		}

		$alive_array = array (
			"guest"  => array( 'name' => 'Only Guests',  'count' => count( $guest_alive_array ), 'url' => home_url( '/role/guest/' ) ),
			"main" => array( 'name' => 'Only Main', 'count' => count( $main_alive_array ), 'url' => home_url( '/role/regular/' ) ),
			"recurring" => array( 'name' => 'Only Recurring', 'count' => count( $recurring_alive_array ), 'url' => home_url( '/role/recurring/' ) ),
		);

		$dead_array = array (
			"guest"  => array( 'name' => 'Only Guests',  'count' => $guest['dead'], 'url' => home_url( '/role/guest/' ) ),
			"main" => array( 'name' => 'Only Main', 'count' => $regular['dead'], 'url' => home_url( '/role/regular/' ) ),
			"recurring" => array( 'name' => 'Only Recurring', 'count' => $recurring['dead'], 'url' => home_url( '/role/recurring/' ) ),
		);

		$array = $alive_array;
		if ( $type == 'dead' ) $array = $dead_array;

		return $array;
	}
	
	static function queer( $count, $type = 'actors' ) {

		$array = array(
			'queer'     => array ( 'name' => 'Queer',  'count' => 0, 'url' => home_url() ),
			'not_queer' => array ( 'name' => 'Not Queer',  'count' => 0, 'url' => home_url() ),
		);

		switch ( $type ) {
			case 'characters':
				$taxonomy                    = self::taxonomy( 'post_type_characters', 'lez_cliches', 'queer-irl' );
				$array['queer']['count']     = $taxonomy['queer-irl']['count'];
				$array['queer']['url']       = home_url( '/cliche/queer-irl/' );
				$array['not_queer']['count'] = ( $count - $array['queer']['count'] );
				break;
			case 'actors':
				$all_actors_query = LWTV_Loops::post_type_query( 'post_type_actors' );
					if ( $all_actors_query->have_posts() ) {
						while ( $all_actors_query->have_posts() ) {
							$all_actors_query->the_post();
							$the_ID   = get_the_id();
							$is_queer = LWTV_Loops::is_actor_queer( $the_ID );
							// And now we set the numbers!
							if ( $is_queer == 'yes' )  $array['queer']['count']++;
							if ( $is_queer == 'no' )   $array['not_queer']['count']++;
						}
						wp_reset_query();
					}
				break;
		}
		
		return $array;
	}
}

new LWTV_Stats_Arrays();