<?php

class LWTV_Theme_Data_Character {
	/**
	 * Generate character Data
	 *
	 * @access public
	 *
	 * @param  string $character_id
	 * @param  string $format
	 *
	 * @return mixed
	 */
	public function make( $character_id, $format ) {

		// Early Bail
		$valid_data  = array( 'dead', 'shows', 'actors', 'cliches', 'oneshow', 'oneactor' );
		$run_as_term = array( 'gender', 'sexuality', 'romantic' );
		if ( ! isset( $character_id ) || ! isset( $format ) || ! in_array( $format, array_merge( $valid_data, $run_as_term ), true ) ) {
			return;
		}

		$do_run = ( in_array( $format, $run_as_term, true ) ) ? 'terms' : $format;
		$output = call_user_func_array( array( $this, $do_run ), array( $character_id, $format ) );

		return $output;
	}

	/**
	 * Build Actor Data
	 *
	 * @param  string $character_id
	 * @param  string $format
	 *
	 * @return array  actor post meta
	 */
	public function actors( $character_id, $format ) {
		$format           = rtrim( $format, 's' );
		$character_actors = get_post_meta( $character_id, 'lezchars_actor', true );
		if ( ! is_array( $character_actors ) && ! empty( $character_actors ) ) {
			$character_actors = array( get_post_meta( $character_id, 'lezchars_actor', true ) );
		}

		return $character_actors;
	}

	/**
	 * Build Cliche data
	 *
	 * @param  string $character_id
	 * @param  string $format
	 *
	 * @return string Cliches with icons
	 */
	public function cliches( $character_id, $format ) {
		$lez_cliches = get_the_terms( $character_id, 'lez_' . $format );
		$cliches     = '';
		if ( $lez_cliches && ! is_wp_error( $lez_cliches ) ) {
			$cliches = '';
			foreach ( $lez_cliches as $the_cliche ) {
				$termicon = get_term_meta( $the_cliche->term_id, 'lez_termsmeta_icon', true );
				$tropicon = $termicon ? $termicon . '.svg' : 'square.svg';
				$icon     = ( new LWTV_Functions() )->symbolicons( $tropicon, 'fa-square' );
				$cliches .= '<a href="' . get_term_link( $the_cliche->slug, 'lez_cliches' ) . '" data-bs-target="tooltip" data-placement="bottom" rel="tag" title="' . $the_cliche->name . '"><span role="img" aria-label="' . $the_cliche->name . '" class="character-cliche ' . $the_cliche->slug . '">' . $icon . '</span></a>&nbsp;';
			}

			return $cliches;
		}
	}

	/**
	 * Build Death Data
	 *
	 * @param  string $character_id
	 * @param  string $format
	 *
	 * @return string Death Icon
	 */
	public function dead( $character_id, $format ) {
		$deadpage = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$icon     = ( new LWTV_Functions() )->symbolicons( 'grim-reaper.svg', 'fa-times-circle' );

		// Show nothing on ARCHIVE pages for dead
		if ( ! empty( $term ) && $format === $term->slug ) {
			return;
		} elseif ( has_term( 'dead', 'lez_cliches', $character_id ) ) {
			return '<span role="img" aria-label="Grim Reaper" title="Grim Reaper" class="charlist-grave">' . $icon . '</span>';
		}
	}

	/**
	 * Build One Actor Display
	 *
	 * @param  string $character_id
	 * @param  string $format
	 *
	 * @return string Data for primary actor (Sara Lance is at it again)
	 */
	public function oneactor( $character_id, $format ) {
		$output      = $format;
		$actors      = get_post_meta( $character_id, 'lezchars_actor', true );
		$actor_value = isset( $actors[0] ) ? $actors[0] : '';

		if ( ! empty( $actor_value ) ) {
			$num_actors = count( $actors );
			$actor_more = ( $num_actors > 1 ) ? ' (plus ' . ( $num_actors - 1 ) . ' more)' : '';
			$actor_post = get_post( $actor_value );
			$actor_name = ( isset( $actor_post->post_title ) && ! is_null( $actor_post->post_title ) ) ? $actor_post->post_title : 'TBD';
			$icon       = ( new LWTV_Functions() )->symbolicons( 'user.svg', 'fa-user' );
			$output     = '<div class="card-meta-item actors">' . $icon;
			if ( get_post_status( $actor_value ) === 'private' ) {
				if ( is_user_logged_in() ) {
					$output .= '<a href="' . get_permalink( $actor_value ) . '">' . get_the_title( $actor_value ) . ' - UNLISTED</a>';
				} else {
					$output .= '<a href="/actor/unknown/">Unknown</a>';
				}
			} elseif ( get_post_status( $actor_value ) !== 'publish' ) {
				$output .= '<span class="disabled-show-link">' . $actor_name . '</span>';
			} else {
				$output .= '<a href="' . get_the_permalink( $actor_post->ID ) . '">' . $actor_name . '</a>';
			}
			$output .= $actor_more . '</div>';

			return $output;
		}
	}

	/**
	 * Build Show Data
	 *
	 * @param  string $character_id
	 * @param  string $format
	 *
	 * @return string Information about show (again, Sara Lance has too many shows)
	 */
	public function oneshow( $character_id, $format ) {
		$output      = $format;
		$all_shows   = get_post_meta( $character_id, 'lezchars_show_group', true );
		$shows_value = isset( $all_shows[0] ) ? $all_shows[0] : '';

		if ( ! empty( $shows_value ) ) {
			// If $shows_value['show'] is an array, de-array it.
			if ( is_array( $shows_value['show'] ) ) {
				$shows_value['show'] = reset( $shows_value['show'] );
			}

			$num_shows = count( $all_shows );
			$show_more = ( $num_shows > 1 ) ? ' (plus ' . ( $num_shows - 1 ) . ' more)' : '';
			$show_post = get_post( $shows_value['show'] );
			$icon      = ( new LWTV_Functions() )->symbolicons( 'tv-hd.svg', 'fa-tv' );
			$output    = '<div class="card-meta-item shows">' . $icon . '<em>';
			if ( get_post_status( $shows_value['show'] ) !== 'publish' ) {
				$output .= '<span class="disabled-show-link">' . $show_post->post_title . '</span>';
			} else {
				$output .= '<a href="' . get_the_permalink( $show_post->ID ) . '">' . $show_post->post_title . '</a>';
			}
			$output .= '</em> (' . $shows_value['type'] . ')' . $show_more . '</div>';

			return $output;
		}
	}

	/**
	 * Build Shows Data
	 *
	 * @param  string $character_id
	 * @param  string $format
	 *
	 * @return array  Array of shows.
	 */
	public function shows( $character_id, $format ) {
		$format = rtrim( $format, 's' );
		return get_post_meta( $character_id, 'lezchars_' . $format . '_group', true );
	}

	/**
	 * Build Term Data
	 *
	 * @param  string $character_id
	 * @param  string $format
	 *
	 * @return string Term information
	 */
	public function terms( $character_id, $format ) {
		$terms = get_the_terms( $character_id, 'lez_' . $format, true );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$output = '';
			foreach ( $terms as $term ) {
				$output .= '<a href="' . get_term_link( $term->slug, 'lez_' . $format ) . '" rel="tag" title="' . $term->name . '">' . $term->name . '</a> ';
			}

			return $output;
		}
	}
}
