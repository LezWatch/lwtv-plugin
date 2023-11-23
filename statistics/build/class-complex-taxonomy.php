<?php

class LWTV_Statistics_Complex_Taxonomy_Build {

	/**
	 * Complex Taxonomy Breakdown
	 * @param  boolean $count [description]
	 * @param  string  $data  [description]
	 * @param  string  $type  [description]
	 * @return array          [description]
	 */
	public function make( $count, $data, $type ) {

		// Default
		$array     = array();
		$post_type = 'post_type_' . $type;
		$do_count  = ( isset( $count ) && 0 !== $count ) ? 'yes' : 'no';

		$transient = 'complex_taxonomy_' . $data . '_' . $type . '_' . $do_count;
		$array     = LWTV_Features_Transients::get_transient( $transient );

		if ( false === $array ) {
			$array = array();

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
						$taxonomy                    = ( new LWTV_Statistics_Taxonomy_Build() )->make( 'post_type_characters', 'lez_cliches', 'queer-irl' );
						$array['queer']['count']     = $taxonomy['queer-irl']['count'];
						$array['queer']['url']       = home_url( '/cliche/queer-irl/' );
						$array['not_queer']['count'] = ( $count - $array['queer']['count'] );
						break;
					case 'actors':
						$all_actors_query = ( new LWTV_Features_Loops() )->post_type_query( 'post_type_actors' );
						if ( $all_actors_query->have_posts() ) {
							$char_array = wp_list_pluck( $all_actors_query->posts, 'ID' );
							wp_reset_query();
						}

						if ( is_array( $char_array ) ) {
							foreach ( $char_array as $the_id ) {
								$is_queer = ( new LWTV_Features_Loops() )->is_actor_queer( $the_id );

								// And now we set the numbers!
								switch ( $is_queer ) {
									case 'yes':
										++$array['queer']['count'];
										break;
									case 'no':
										++$array['not_queer']['count'];
										break;
								}
							}
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
					$count_terms_queery  = ( new LWTV_Features_Loops() )->tax_query( $post_type, 'lez_' . $data, 'slug', $term_slug, 'IN' );
					$term_count          = $count_terms_queery->post_count;
					$array[ $term_slug ] = array(
						'count' => $term_count,
						'name'  => $term_name,
						'url'   => $term_link,
					);
					$count              -= $term_count;
				}

				if ( 'yes' === $do_count ) {
					$array['none'] = array(
						'count' => $count,
						'name'  => 'None',
						'url'   => '',
					);
				}
			}

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}
