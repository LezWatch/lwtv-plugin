<?php

class LWTV_Statistics_Taxonomy_Breakdowns_Build {

	/**
	 * Calculate statistics for complicated taxonomies
	 *
	 * @access public
	 * @static
	 * @param mixed $count   - integer; number of posts.
	 * @param mixed $format  - string; format of stats (i.e. lists, piecharts, etc).
	 * @param mixed $data    - string; [main taxonomy]_[term of main]_[metadata to parse].
	 * @param mixed $subject - string; post type (shows, characters).
	 * @return void
	 */
	public function make( $count, $format, $data, $subject ) {
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

		/**
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
		$data_meta = ( isset( $pieces[2] ) && in_array( $pieces[2], $valid_subtaxes ) ) ? $pieces[2] : 'all'; // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict

		/**
		 * Get the taxonomy data.
		 *
		 * This is the nation or station (term) we're going to process.
		 * If no specific term provided, we'll process the whole taxonomy.
		 */
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

		/**
		 * Parse the taxonomy.
		 *
		 * Either we get the information for ALL stations/nations, or just one.
		 */
		foreach ( $taxonomy as $the_tax ) {
			$characters = 0;
			$shows      = 0;
			$dead       = 0;
			$dead_shows = 0;
			$big_data   = array();
			// This is the name of the nation/station.
			$slug = ( ! isset( $the_tax->slug ) ) ? $the_tax['slug'] : $the_tax->slug;
			// This is the display name (used by stacked barcharts).
			$name = ( ! isset( $the_tax->name ) ) ? $the_tax['name'] : $the_tax->name;
			// Get the posts.
			$queery = ( new LWTV_Features_Loops() )->tax_query( 'post_type_shows', 'lez_' . $data_main, 'slug', $slug );

			if ( $queery->have_posts() ) {
				$all_shows = wp_list_pluck( $queery->posts, 'ID' );
				wp_reset_query();
			}

			if ( isset( $all_shows ) && is_array( $all_shows ) ) {
				foreach ( $all_shows as $show_id ) {
					// This data is universal for every thing we process.
					++$shows;
					$dead       += get_post_meta( $show_id, 'lezshows_dead_count', true );
					$characters += get_post_meta( $show_id, 'lezshows_char_count', true );
					if ( has_term( 'dead-queers', 'lez_tropes', $show_id ) ) {
						++$dead_shows;
					}
					// Get the data...
					if ( 'all' !== $data_meta && 'stackedbar' !== $format ) {
						// This is for when we show a specific taxonomy for a specific nation/station.
						// Example: Sexuality for Argentina.
						if ( in_array( $data_meta, $extra_subtaxes ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
							// Get all the terms for this show.
							$big_data_array = get_the_terms( $show_id, 'lez_' . $data_meta );
							if ( ! empty( $big_data_array ) && ! is_wp_error( $big_data_array ) ) {
								foreach ( $big_data_array as $big_data_item ) {
									if ( in_array( $data_meta, $extra_subtaxes ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
										if ( ! isset( $big_data[ $big_data_item->name ] ) ) {
											$big_data[ $big_data_item->name ] = 0;
										}
										++$big_data[ $big_data_item->name ];
									} else {
										if ( ! isset( $big_data[ $big_data_item->slug ] ) ) {
											$big_data[ $big_data_item->slug ] = 0;
										}
										++$big_data[ $big_data_item->slug ];
									}
								}
							}
						} else {
							// Otherwise, we can grab the meta-data from each show.
							$big_data_array = get_post_meta( $show_id, 'lezshows_char_' . $data_meta );
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
							$big_data_array = get_the_terms( $show_id, 'lez_' . $data_meta );
							if ( ! empty( $big_data_array ) && ! is_wp_error( $big_data_array ) ) {
								foreach ( $big_data_array as $big_data_item ) {
									if ( ! isset( $big_data[ $big_data_item->slug ] ) ) {
										$big_data[ $big_data_item->slug ] = 0;
									}
									++$big_data[ $big_data_item->slug ];
								}
							}
						} else {
							// We can use the post meta.
							$big_data_array = get_post_meta( $show_id, 'lezshows_char_' . $data_meta );
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
							$big_data_array = get_post_meta( $show_id, 'lezshows_char_' . $meta );
							foreach ( array_shift( $big_data_array ) as $big_data_meta => $big_data_count ) {
								if ( ! isset( $big_data[ $big_data_meta ] ) ) {
									$big_data[ $big_data_meta ] = 0;
								}
								$big_data[ $big_data_meta ] += $big_data_count;
							}
						}
					}
				}
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
							foreach ( $big_data as $big_name => $big_count ) {
								$maybe_url = get_term_by( 'name', $big_name, 'lez_' . $data_meta );
								$url       = ( $maybe_url->slug ) ?? $big_name;
								$array[]   = array(
									'name'  => $big_name,
									'count' => $big_count,
									'url'   => '/' . rtrim( $data_meta, 's' ) . '/' . $url,
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
}
