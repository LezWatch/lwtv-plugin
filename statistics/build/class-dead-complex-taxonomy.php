<?php

class LWTV_Statistics_Dead_Complex_Taxonomy_Build {

	/**
	 * Complex death taxonomies for stations and nations.
	 *
	 * @access public
	 * @static
	 * @param mixed $type - string.
	 * @return array.
	 */
	public function make( $type ) {

		// Defaults.
		$valid_types = array( 'stations', 'country' );

		// Bail early.
		if ( ! in_array( $type, $valid_types, true ) ) {
			return;
		}

		$transient = 'dead_complex_taxonomy_lez_' . $type;
		$array     = LWTV_Features_Transients::get_transient( $transient );

		if ( false === $array ) {
			$array    = array();
			$taxonomy = get_terms( 'lez_' . $type );

			// For each station/nation, we need to count the data.
			foreach ( $taxonomy as $the_tax ) {
				// This is the name of the nation/station.
				$slug = ( ! isset( $the_tax->slug ) ) ? $the_tax['slug'] : $the_tax->slug;
				$name = ( ! isset( $the_tax->name ) ) ? $the_tax['name'] : $the_tax->name;

				// Get the posts.
				$queery = ( new LWTV_Features_Loops() )->tax_query( 'post_type_shows', 'lez_' . $type, 'slug', $slug );

				// Process the posts.
				if ( $queery->have_posts() ) {
					// Defaults.
					$shows      = 0;
					$characters = 0;
					$dead_shows = 0;
					$dead_chars = 0;

					foreach ( $queery->posts as $show ) {
						// This data is universal for every thing we process.
						++$shows;
						$dead_chars += get_post_meta( $show->ID, 'lezshows_dead_count', true );
						$characters += get_post_meta( $show->ID, 'lezshows_char_count', true );
						if ( has_term( 'dead-queers', 'lez_tropes', $show->ID ) ) {
							++$dead_shows;
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

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}
