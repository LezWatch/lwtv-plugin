<?php

class LWTV_Theme_Taxonomy_Archive_Title {
	/**
	 * Taxonomy Archive Title
	 *
	 * Take the data from the taxonomy to determine a dynamic title.
	 *
	 * @access public
	 * @param string $location
	 * @param string $post_type
	 * @param string $taxonomy
	 *
	 * @return string Adjusted title.
	 */
	public function make( $location, $post_type, $taxonomy ) {
		// Bail if not set
		if ( ! isset( $location ) || ! isset( $post_type ) || ! isset( $taxonomy ) ) {
			return;
		}

		$title_prefix = '';
		$title_suffix = '';
		$term         = get_term_by( 'slug', get_query_var( 'term' ), $taxonomy );
		$termicon     = get_term_meta( $term->term_id, 'lez_termsmeta_icon', true );

		// FA defaults
		switch ( $taxonomy ) {
			case 'lez_cliches':
				$fa  = 'fa-bell';
				$svg = $termicon ? $termicon . '.svg' : 'bell.svg';
				break;
			case 'lez_tropes':
				$fa  = 'fa-pastafarianism';
				$svg = $termicon ? $termicon . '.svg' : 'octopus.svg';
				break;
			case 'lez_formats':
				$fa  = 'fa-film';
				$svg = $termicon ? $termicon . '.svg' : 'film-strip.svg';
				break;
			case 'lez_genres':
				$fa  = 'fa-th-large';
				$svg = $termicon ? $termicon . '.svg' : 'blocks.svg';
				break;
			case 'lez_intersections':
				$fa  = 'fa-flag';
				$svg = $termicon ? $termicon . '.svg' : 'flag-wave.svg';
				break;
			case 'lez_gender':
			case 'lez_actor_gender':
				$fa  = 'fa-female';
				$svg = 'female.svg';
				break;
			case 'lez_sexuality':
			case 'lez_actor_sexuality':
				$fa  = 'fa-venus-double';
				$svg = 'venus-double.svg';
				break;
			case 'lez_romantic':
				$fa  = 'fa-heartbeat';
				$svg = 'user-heart.svg';
				break;
			case 'lez_stars':
				$fa  = 'fa-star';
				$svg = 'star.svg';
				break;
			case 'lez_triggers':
				$fa  = 'fa-exclamation-triangle';
				$svg = 'warning.svg';
				break;
			case 'lez_stations':
				$fa  = 'fa-bullhorn';
				$svg = 'satellite-signal.svg';
				break;
			case 'lez_country':
				$fa  = 'fa-globe';
				$svg = 'globe.svg';
				break;
			default:
				$fa  = 'fa-square';
				$svg = 'square.svg';
				break;
		}

		// Build Icon:
		$icon = ( new LWTV_Functions() )->symbolicons( $svg, $fa );

		switch ( $post_type ) {
			case 'post_type_characters':
				$title_suffix = ' Characters';
				break;
			case 'post_type_actors':
				$title_suffix = ' Actors';
				break;
			case 'post_type_shows':
				$title_suffix = ' TV Shows';

				// TV Shows are harder to have titles
				switch ( $taxonomy ) {
					case 'lez_stars':
						$title_suffix = '';
						$title_prefix = 'TV Shows with ';
						break;
					case 'lez_country':
						$title_suffix = ' Based TV Shows';
						$title_prefix = '';
						break;
					case 'lez_formats':
						$title_suffix = '';
						break;
					case 'lez_triggers':
						$title_prefix = 'TV Shows with ';
						$title_suffix = ' Trigger Warnings';
						break;
				}
				break;
		}

		switch ( $location ) {
			case 'prefix':
				$return = $title_prefix;
				break;
			case 'suffix':
				$return = $title_suffix;
				break;
			case 'icon':
				$return = $icon;
				break;
		}

		return $return;
	}
}
