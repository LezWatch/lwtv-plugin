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
	public static function taxonomy( $post_type, $taxonomy, $terms = '', $operator = 'IN' ) {
		$array = array();

		// If no term provided, use get_terms for the taxonomy.
		$taxonomies = ( '' === $terms ) ? get_terms( $taxonomy ) : array( $terms );

		foreach ( $taxonomies as $term ) {
			$term_obj          = ( '' !== $terms ) ? get_term_by( 'slug', $term, $taxonomy, 'ARRAY_A' ) : '';
			$term_link         = get_term_link( $term, $taxonomy );
			$term_slug         = ( '' === $terms ) ? $term->slug : $terms;
			$term_name         = ( '' === $terms ) ? $term->name : $term_obj['name'];
			$count_terms_query = LWTV_Loops::tax_query( $post_type, $taxonomy, 'slug', $term_slug, $operator );
			$term_count        = $count_terms_query->post_count;

			$array[ $term_slug ] = array(
				'count' => $term_count,
				'name'  => $term_name,
				'url'   => $term_link,
			);
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
	public static function dead_taxonomy( $post_type, $taxonomy ) {

		$array      = array();
		$taxonomies = get_terms( $taxonomy );

		foreach ( $taxonomies as $term ) {
			$queery = LWTV_Loops::tax_two_query( $post_type, $taxonomy, 'slug', $term->slug, 'lez_cliches', 'slug', 'dead' );

			$array[ $term->slug ] = array(
				'count' => $queery->post_count,
				'name'  => $term->name,
				'url'   => get_term_link( $term ),
			);
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
	public static function dead_role() {
		$array        = array();
		$all_the_dead = LWTV_Loops::tax_query( 'post_type_characters', 'lez_cliches', 'slug', 'dead' );
		$by_role      = array(
			'regular'   => 0,
			'guest'     => 0,
			'recurring' => 0,
		);

		if ( $all_the_dead->have_posts() ) {

			foreach ( $all_the_dead->posts as $dead ) {
				$all_shows = get_post_meta( $dead->ID, 'lezchars_show_group', true );
				foreach ( $all_shows as $each_show ) {
					if ( 'regular' === $each_show['type'] ) {
						$by_role['regular']++;
					}
					if ( 'guest' === $each_show['type'] ) {
						$by_role['guest']++;
					}
					if ( 'recurring' === $each_show['type'] ) {
						$by_role['recurring']++;
					}
				}
			}
			wp_reset_query();
		}

		$array = array(
			'regular'   => array(
				'count' => $by_role['regular'],
				'name'  => 'Regular',
				'url'   => home_url( '/role/regular/' ),
			),
			'guest'     => array(
				'count' => $by_role['guest'],
				'name'  => 'Guest',
				'url'   => home_url( '/role/guest/' ),
			),
			'recurring' => array(
				'count' => $by_role['recurring'],
				'name'  => 'Recurring',
				'url'   => home_url( '/role/recurring/' ),
			),
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
	public static function dead_meta_tax( $post_type, $meta_array, $key, $taxonomy = 'lez_cliches', $field = 'dead' ) {
		$array = array();

		foreach ( $meta_array as $value ) {
			$query           = LWTV_Loops::post_meta_and_tax_query( $post_type, $key, $value, $taxonomy, 'slug', $field );
			$array[ $value ] = array(
				'count' => $query->post_count,
				'name'  => ucfirst( $value ),
				'url'   => home_url( '/cliche/' . $value ),
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
	public static function meta( $post_type, $meta_array, $key, $data, $compare = '=' ) {
		$array = array();
		foreach ( $meta_array as $value ) {
			$meta_query      = LWTV_Loops::post_meta_query( $post_type, $key, $value, $compare );
			$array[ $value ] = array(
				'count' => $meta_query->post_count,
				'name'  => ucfirst( $value ),
				'url'   => home_url( '/' . $data . '/' . lcfirst( $value ) . '/' ),
			);
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
	public static function yes_no( $post_type, $data, $count ) {

		$array = array(
			'no'  => array(
				'count' => '0',
				'name'  => 'No',
				'url'   => '',
			),
			'yes' => array(
				'count' => '0',
				'name'  => 'Yes',
				'url'   => '',
			),
		);

		// Define the options
		switch ( $data ) {
			case 'weloveit':
				$meta_array = array(
					'on',
				);
				$key        = 'lezshows_worthit_show_we_love';
				$compare    = '=';
				break;
			case 'current':
				$meta_array = array(
					'current',
					'notcurrent',
				);
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
	 * Calculate taxonomy_breakdowns statistics
	 *
	 * @access public
	 * @static
	 * @param mixed $count   - integer; number of posts.
	 * @param mixed $format  - string; format of stats (i.e. lists, piecharts, etc).
	 * @param mixed $data    - string; [main taxonomy]_[term of main]_[metadata to parse].
	 * @param mixed $subject - string; post type (shows, characters).
	 * @return void
	 */
	public static function taxonomy_breakdowns( $count, $format, $data, $subject ) {
		// Set defaults.
		$array = array();
		// Arrays of the secondary taxonomies we care about.
		$main_subtaxes  = array(
			'gender',
			'sexuality',
			'romantic',
		);
		$extra_subtaxes = array(
			'cliches',
			'tropes',
			'intersections',
			'formats',
			'stations',
			'country',
		);
		$valid_subtaxes = array_merge( $main_subtaxes, $extra_subtaxes );
		/*
		 * This is confusing, I know.
		 * [main_term_meta]
		 * [main taxonomy]_[term of main]_[metadata to parse]
		 * ex: [country_all_gender]
		 *     [station_abc_sexuality]
		 *     [country_usa_all]
		 *     [showform_tv-show_usa] (yes, weirder!)
		 */
		$pieces    = explode( '_', $data );
		$data_main = $pieces[0];
		$data_term = ( isset( $pieces[1] ) ) ? $pieces[1] : 'all';
		$data_meta = ( isset( $pieces[2] ) && in_array( $pieces[2], array_keys( $valid_subtaxes ) ) ) ? $pieces[2] : 'all'; // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		// Get the taxonomy data.
		// This is the nation or station (term) we're going to process.
		// If no specific term provided, we'll process the whole taxonomy.
		if ( 'all' !== $data_term ) {
			$tax_term = get_term_by( 'slug', $data_term, 'lez_' . $data_main );
			$taxonomy = array(
				$data_term => array(
					'name' => $tax_term->name,
					'slug' => $data_term,
				),
			);
		} else {
			$taxonomy = get_terms( 'lez_' . $data_main );
		}
		// Parse the taxonomy.
		// Either we get the information for ALL stations/nations, or just one.
		foreach ( $taxonomy as $the_tax ) {
			$characters = 0;
			$shows      = 0;
			$dead       = 0;
			$big_data   = array();
			// This is the name of the nation/station.
			$slug = ( ! isset( $the_tax->slug ) ) ? $the_tax['slug'] : $the_tax->slug;
			// This is the display name (used by stacked barcharts).
			$name = ( ! isset( $the_tax->name ) ) ? $the_tax['name'] : $the_tax->name;
			// Get the posts.
			$queery = LWTV_Loops::tax_query( 'post_type_shows', 'lez_' . $data_main, 'slug', $slug );
			// Process the posts.
			if ( $queery->have_posts() ) {
				// Defaults.
				$shows      = 0;
				$dead       = 0;
				$characters = 0;
				$dead_shows = 0;
				foreach ( $queery->posts as $show ) {
					// This data is universal for every thing we process.
					$shows++;
					$dead       += get_post_meta( $show->ID, 'lezshows_dead_count', true );
					$characters += get_post_meta( $show->ID, 'lezshows_char_count', true );
					if ( has_term( 'dead-queers', 'lez_tropes', $show->ID ) ) {
						$dead_shows++;
					}
					// Get the data...
					if ( 'all' !== $data_meta && 'stackedbar' !== $format ) {
						// This is for when we show a specific taxonomy for a specific nation/station.
						// Example: Sexuality for Argentina.
						if ( in_array( $data_meta, $extra_subtaxes ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
							// Get all the terms for this show.
							$big_data_array = get_the_terms( $show->ID, 'lez_' . $data_meta );
							if ( ! empty( $big_data_array ) && ! is_wp_error( $big_data_array ) ) {
								foreach ( $big_data_array as $big_data_item ) {
									if ( in_array( $data_meta, $extra_subtaxes ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
										if ( ! isset( $big_data[ $big_data_item->name ] ) ) {
											$big_data[ $big_data_item->name ] = 0;
										}
										$big_data[ $big_data_item->name ]++;
									} else {
										if ( ! isset( $big_data[ $big_data_item->slug ] ) ) {
											$big_data[ $big_data_item->slug ] = 0;
										}
										$big_data[ $big_data_item->slug ]++;
									}
								}
							}
						} else {
							// Otherwise, we can grab the meta-data from each show.
							$big_data_array = get_post_meta( $show->ID, 'lezshows_char_' . $data_meta );
							foreach ( array_shift( $big_data_array ) as $big_data_meta => $big_data_count ) {
								if ( ! isset( $big_data[ $big_data_meta ] ) ) {
									$big_data[ $big_data_meta ] = 0;
								}
								$big_data[ $big_data_meta ] += $big_data_count;
							}
						}
					} elseif ( 'all' !== $data_meta && 'stackedbar' === $format ) {
						if ( in_array( $data_meta, $extra_subtaxes ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
							// Get all the terms for this show.
							$big_data_array = get_the_terms( $show->ID, 'lez_' . $data_meta );
							if ( ! empty( $big_data_array ) && ! is_wp_error( $big_data_array ) ) {
								foreach ( $big_data_array as $big_data_item ) {
									if ( ! isset( $big_data[ $big_data_item->slug ] ) ) {
										$big_data[ $big_data_item->slug ] = 0;
									}
									$big_data[ $big_data_item->slug ]++;
								}
							}
						} else {
							// We can use the post meta.
							$big_data_array = get_post_meta( $show->ID, 'lezshows_char_' . $data_meta );
							foreach ( array_shift( $big_data_array ) as $big_data_meta => $big_data_count ) {
								if ( ! isset( $big_data[ $big_data_meta ] ) ) {
									$big_data[ $big_data_meta ] = 0;
								}
								$big_data[ $big_data_meta ] += $big_data_count;
							}
						}
					} elseif ( 'all' === $data_meta && 'all' !== $data_term ) {
						// If the data_meta is "all" then we are on the OVERVIEW tab for ONE nation/station.
						foreach ( $main_subtaxes as $meta ) {
							$big_data_array = get_post_meta( $show->ID, 'lezshows_char_' . $meta );
							foreach ( array_shift( $big_data_array ) as $big_data_meta => $big_data_count ) {
								if ( ! isset( $big_data[ $big_data_meta ] ) ) {
									$big_data[ $big_data_meta ] = 0;
								}
								$big_data[ $big_data_meta ] += $big_data_count;
							}
						}
					}
				}
				wp_reset_query();
			}
			// Determine what kind of array we need to show...
			switch ( $format ) {
				case 'barchart':
					if ( 'all' !== $data_term && 'all' !== $data_meta ) {
						foreach ( $big_data as $char_name => $char_count ) {
							$array[] = array(
								'name'  => $char_name,
								'count' => $char_count,
							);
						}
					} elseif ( 'all' !== $data_term && 'all' === $data_meta ) {
						$array['shows'] = array(
							'name'  => 'Shows',
							'count' => $shows,
						);
						$array['chars'] = array(
							'name'  => 'Characters',
							'count' => $characters,
						);
						$array['death'] = array(
							'name'  => 'Dead Characters',
							'count' => $dead,
						);
						foreach ( $big_data as $ctax_name => $ctax_count ) {
							if ( 0 !== $ctax_count ) {
								$array[ $ctax_name ] = array(
									'name'  => ucfirst( $ctax_name ),
									'count' => $ctax_count,
								);
							}
						}
					} else {
						$array = self::taxonomy( 'post_type_shows', 'lez_' . $data_main );
					}
					break;
				case 'percentage':
				case 'piechart':
				case 'list':
					if ( 'all' !== $data_term ) {
						if ( 'all' !== $data_meta ) {
							foreach ( $big_data as $char_name => $char_count ) {
								$array[] = array(
									'name'  => $char_name,
									'count' => $char_count,
									'url'   => '#',
								);
							}
						} else {
							$array['shows'] = array(
								'count' => $shows,
								'name'  => 'Shows',
								'url'   => '#',
							);
							$array['chars'] = array(
								'count' => $characters,
								'name'  => 'Characters',
								'url'   => '#',
							);
						}
					} else {
						$array = self::taxonomy( 'post_type_shows', 'lez_' . $data_main );
					}
					break;
				case 'count':
					$array = count( $taxonomy );
					break;
				case 'stackedbar':
					$array[ $slug ] = array(
						'name'       => $name,
						'count'      => $shows,
						'characters' => $characters,
						'dataset'    => $big_data,
					);
			}
		}
		if ( 'count' === $format ) {
			switch ( $subject ) {
				case 'characters':
					$array = $characters;
					break;
				case 'shows':
					$array = $shows;
					break;
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
	public static function dead_basic( $subject, $output ) {

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

		$array = self::taxonomy( 'post_type_' . $subject, $taxonomy, $terms );

		switch ( $subject ) {
			case 'characters':
				$array['dead'] = array(
					'count' => ( $array['dead']['count'] ),
					'name'  => 'Dead Characters',
					'url'   => home_url( '/cliche/dead/' ),
				);
				$count         = $array['dead']['count'];
				break;
			case 'shows':
				$array['dead-queers'] = array(
					'count' => ( $array['dead-queers']['count'] ),
					'name'  => 'Shows with Dead',
					'url'   => home_url( '/trope/dead-queers/' ),
				);
				$count                = $array['dead-queers']['count'];
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
	public static function dead_year() {

		// Create the date with regards to timezones
		$tz        = 'America/New_York';
		$timestamp = time();
		$dt        = new DateTime( 'now', new DateTimeZone( $tz ) ); //first argument "must" be a string
		$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp
		$this_year = $dt->format( 'Y' );

		// Death by year
		$year_first           = FIRST_LWTV_YEAR;
		$year_deathlist_array = array();
		foreach ( range( $this_year, $year_first ) as $x ) {
			$year_deathlist_array[ $x ] = $x;
		}

		$year_death_array = array();
		foreach ( $year_deathlist_array as $year ) {
			$year_death_query = LWTV_Loops::post_meta_and_tax_query( 'post_type_characters', 'lezchars_death_year', $year, 'lez_cliches', 'slug', 'dead', 'REGEXP' );

			$year_death_array[ $year ] = array(
				'name'  => $year,
				'count' => $year_death_query->post_count,
				'url'   => home_url( '/this-year/' . $year . '/' ),
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
	public static function dead_shows( $format ) {

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

				$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
				$show_name = strtolower( $show_name );

				$noneshow_death_array[ $show_name ] = array(
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

				$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
				$show_name = strtolower( $show_name );

				// Loop of characters who MIGHT be in this show
				$this_show_characters_query = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );

				$fulldeathcount = get_post_meta( $show_id, 'lezshows_dead_count', true );
				$allcharcount   = get_post_meta( $show_id, 'lezshows_char_count', true );

				if ( $fulldeathcount === $allcharcount ) {
					$fullshow_death_array[ $show_name ] = array(
						'url'    => get_permalink( $show_id ),
						'name'   => get_the_title( $show_id ),
						'status' => get_post_status( $show_id ),
					);
				} else {
					$someshow_death_array[ $show_name ] = array(
						'url'    => get_permalink( $show_id ),
						'name'   => get_the_title( $show_id ),
						'status' => get_post_status( $show_id ),
					);
				}
			}
			wp_reset_query();
		}

		if ( 'simple' === $format ) {
			$array = array(
				'all'  => array(
					'name'  => 'All characters are dead',
					'count' => count( $fullshow_death_array ),
					'url'   => '',
				),
				'some' => array(
					'name'  => 'Some characters are dead',
					'count' => count( $someshow_death_array ),
					'url'   => '',
				),
				'none' => array(
					'name'  => 'No characters are dead',
					'count' => count( $noneshow_death_array ),
					'url'   => '',
				),
			);
		}

		return $array;
	}


	/**
	 * Complex death taxonomies.
	 *
	 * @access public
	 * @static
	 * @param mixed $type - string.
	 * @return array.
	 */
	public static function dead_complex_taxonomy( $type ) {
		// Defaults.
		$valid_types = array( 'stations', 'country' );
		$array       = array();

		// Bail early.
		if ( ! in_array( $type, $valid_types, true ) ) {
			return;
		}

		// Parse the taxonomy.
		$taxonomy = get_terms( 'lez_' . $type );

		// For each station/nation, we need to count the data.
		foreach ( $taxonomy as $the_tax ) {
			// This is the name of the nation/station.
			$slug = ( ! isset( $the_tax->slug ) ) ? $the_tax['slug'] : $the_tax->slug;
			$name = ( ! isset( $the_tax->name ) ) ? $the_tax['name'] : $the_tax->name;

			// Get the posts.
			$queery = LWTV_Loops::tax_query( 'post_type_shows', 'lez_' . $type, 'slug', $slug );

			// Process the posts.
			if ( $queery->have_posts() ) {
				// Defaults.
				$shows      = 0;
				$characters = 0;
				$dead_shows = 0;
				$dead_chars = 0;

				foreach ( $queery->posts as $show ) {
					// This data is universal for every thing we process.
					$shows++;
					$dead_chars += get_post_meta( $show->ID, 'lezshows_dead_count', true );
					$characters += get_post_meta( $show->ID, 'lezshows_char_count', true );
					if ( has_term( 'dead-queers', 'lez_tropes', $show->ID ) ) {
						$dead_shows++;
					}
				}

				$array[] = array(
					'count'      => $dead_chars,
					'name'       => $name,
					'url'        => get_term_link( $the_tax ),
					'characters' => $characters,
					'shows'      => $shows,
				);
			}
		}

		return $array;
	}

	/*
	 * Statistics Scores
	 *
	 * @return array
	 */
	public static function scores( $post_type ) {
		$the_queery = LWTV_Loops::post_type_query( $post_type );
		$array      = array();
		if ( $the_queery->have_posts() ) {
			while ( $the_queery->have_posts() ) {
				$the_queery->the_post();
				$post               = get_post();
				$array[ $post->ID ] = array(
					'id'    => $post->ID,
					'count' => get_post_meta( $post->ID, 'lezshows_the_score', true ),
					'url'   => get_the_permalink( $post->ID ),
				);
			}
			wp_reset_query();
		}

		return $array;
	}

	/**
	 * Statistics: Actors and Characters
	 *
	 * @access public
	 * @static
	 * @param string $type (default: 'chars')
	 * @return void
	 */
	public static function actor_chars( $type = 'characters' ) {
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

				// Check key
				if ( ! array_key_exists( $key, $array ) && is_numeric( $key ) ) {
					$array[ $key ] = array(
						'name'  => $key . ' ' . $name,
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
	public static function show_roles( $type = 'dead' ) {
		// List of shows
		$all_shows_query = LWTV_Loops::post_type_query( 'post_type_shows' );

		$guest_alive_array     = array();
		$recurring_alive_array = array();
		$main_alive_array      = array();
		$guest_dead_array      = array();
		$recurring_dead_array  = array();
		$main_dead_array       = array();

		if ( $all_shows_query->have_posts() ) {

			while ( $all_shows_query->have_posts() ) {
				$all_shows_query->the_post();
				$show_id = get_the_id();

				$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
				$show_name = strtolower( $show_name );

				$role_loop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );

				if ( $role_loop->have_posts() ) {

					$guest     = array(
						'alive' => 0,
						'dead'  => 0,
					);
					$regular   = array(
						'alive' => 0,
						'dead'  => 0,
					);
					$recurring = array(
						'alive' => 0,
						'dead'  => 0,
					);

					$char_id     = get_the_id();
					$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true );

					if ( '' !== $shows_array ) {

						foreach ( $shows_array as $each_show ) {
							if ( 'guest' === $char_show['type'] ) {
								$guest['alive']++;
								if ( has_term( 'dead', 'lez_cliches', $char_id ) ) {
									$guest['dead']++;
								}
							}
							if ( 'regular' === $char_show['type'] ) {
								$regular['alive']++;
								if ( has_term( 'dead', 'lez_cliches', $char_id ) ) {
									$regular['dead']++;
								}
							}
							if ( 'recurring' === $char_show['type'] ) {
								$recurring['alive']++;
								if ( has_term( 'dead', 'lez_cliches', $char_id ) ) {
									$recurring['dead']++;
								}
							}
						}
					}

					// Make Alive Query
					if ( 0 === $regular['alive'] && 0 !== $recurring['alive'] && 0 === $guest['alive'] ) {
						$recurring_alive_array[ $show_name ] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					if ( 0 === $regular['alive'] && 0 === $recurring['alive'] && 0 !== $guest['alive'] ) {
						$guest_alive_array[ $show_name ] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					if ( 0 !== $regular['alive'] && 0 === $guest['alive'] && 0 === $recurring['alive'] ) {
						$main_alive_array[ $show_name ] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}

					// Make Dead Data
					if ( 0 === $regular['dead'] && 0 !== $recurring['dead'] && 0 === $guest['dead'] ) {
						$recurring_dead_array[ $show_name ] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					if ( 0 === $regular['dead'] && 0 === $recurring['dead'] && 0 !== $guest['dead'] ) {
						$guest_dead_array[ $show_name ] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					if ( 0 !== $regular['dead'] && 0 === $guest['dead'] && 0 === $recurring['dead'] ) {
						$main_dead_array[ $show_name ] = array(
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

		$alive_array = array(
			'guest'     => array(
				'name'  => 'Only Guests',
				'count' => count( $guest_alive_array ),
				'url'   => home_url( '/role/guest/' ),
			),
			'main'      => array(
				'name'  => 'Only Main',
				'count' => count( $main_alive_array ),
				'url'   => home_url( '/role/regular/' ),
			),
			'recurring' => array(
				'name'  => 'Only Recurring',
				'count' => count( $recurring_alive_array ),
				'url'   => home_url( '/role/recurring/' ),
			),
		);

		$dead_array = array(
			'guest'     => array(
				'name'  => 'Only Guests',
				'count' => $guest['dead'],
				'url'   => home_url( '/role/guest/' ),
			),
			'main'      => array(
				'name'  => 'Only Main',
				'count' => $regular['dead'],
				'url'   => home_url( '/role/regular/' ),
			),
			'recurring' => array(
				'name'  => 'Only Recurring',
				'count' => $recurring['dead'],
				'url'   => home_url( '/role/recurring/' ),
			),
		);

		$array = $alive_array;
		if ( 'dead' === $type ) {
			$array = $dead_array;
		}

		return $array;
	}

	public static function complex_taxonomy( $count, $data, $type ) {

		// Default
		$array     = array();
		$post_type = 'post_type_' . $type;

		if ( 'queer-irl' === $data ) {

			$array = array(
				'queer'     => array(
					'name'  => 'Queer',
					'count' => 0,
					'url'   => home_url(),
				),
				'not_queer' => array(
					'name'  => 'Not Queer',
					'count' => 0,
					'url'   => home_url(),
				),
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
							$the_id   = get_the_id();
							$is_queer = LWTV_Loops::is_actor_queer( $the_id );

							// And now we set the numbers!
							switch ( $is_queer ) {
								case 'yes':
									$array['queer']['count']++;
									break;
								case 'no':
									$array['not_queer']['count']++;
									break;
							}
						}
						wp_reset_query();
					}
					break;
			}
		} else {
			// Get all the terms
			$taxonomies = get_terms( 'lez_' . $data );
			foreach ( $taxonomies as $term ) {
				$term_obj            = get_term_by( 'slug', $term, $data, 'ARRAY_A' );
				$term_link           = get_term_link( $term, $data );
				$term_slug           = $term->slug;
				$term_name           = $term->name;
				$count_terms_queery  = LWTV_Loops::tax_query( $post_type, 'lez_' . $data, 'slug', $term_slug, 'IN' );
				$term_count          = $count_terms_queery->post_count;
				$array[ $term_slug ] = array(
					'count' => $term_count,
					'name'  => $term_name,
					'url'   => $term_link,
				);
				$count              -= $term_count;
			}

			if ( isset( $count ) && 0 !== $count ) {
				$array['none'] = array(
					'count' => $count,
					'name'  => 'None',
					'url'   => '',
				);
			}
		}

		return $array;
	}
}

new LWTV_Stats_Arrays();
