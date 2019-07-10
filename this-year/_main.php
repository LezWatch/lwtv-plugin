<?php
/**
 * Functions that power the "This Year" pages
 *
 * @package LezWatch.TV
 */

class LWTV_This_Year {

	public static function display( $thisyear ) {
		$thisyear = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		echo '<div class="thisyear-container">';
			self::dead( $thisyear );
			self::shows( $thisyear );
			self::navigation( $thisyear );
		echo '</div>';
	}

	public function dead( $thisyear ) {
		$thisyear    = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		$dead_loop   = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_death_year', $thisyear, 'REGEXP' );
		$dead_queery = wp_list_pluck( $dead_loop->posts, 'ID' );
		?>
		<h2><a name="died">Characters Died This Year (<?php echo (int) $dead_loop->post_count; ?>)</a></h2>

		<?php
		// List all queers and the year they died
		if ( $dead_loop->have_posts() ) {
			$death_list_array = array();
			foreach ( $dead_queery as $dead_char ) {
				// Since SOME characters have multiple shows, we force this to be an array
				$show_ids   = get_post_meta( $dead_char, 'lezchars_show_group', true );
				$show_title = array();
				foreach ( $show_ids as $each_show ) {
					// if the show isn't published, no links
					if ( get_post_status( $each_show['show'] ) !== 'publish' ) {
						array_push( $show_title, '<em><span class="disabled-show-link">' . get_the_title( $each_show['show'] ) . '</span></em> (' . $each_show['type'] . ' character)' );
					} else {
						array_push( $show_title, '<em><a href="' . get_permalink( $each_show['show'] ) . '">' . get_the_title( $each_show['show'] ) . '</a></em> (' . $each_show['type'] . ' character)' );
					}
				}
				$show_info = implode( ', ', $show_title );
				// Only extract the date for this year and convert to unix time
				// Jesus, I hope no one dies twice in the same year ... SARA
				$died_date = get_post_meta( $dead_char, 'lezchars_death_year', true );
				foreach ( $died_date as $date ) {
					if ( (int) substr( $date, 0, 4 ) === (int) substr( $date, 0, 4 ) ) {
						$died_year  = substr( $date, 0, 4 );
						$died_array = date_parse_from_format( 'Y-m-d', $date );
					} else {
						$died_year  = substr( $date, -4 );
						$died_array = date_parse_from_format( 'm/d/Y', $date );
					}
					if ( $died_year === $thisyear ) {
						$died = mktime( $died_array['hour'], $died_array['minute'], $died_array['second'], $died_array['month'], $died_array['day'], $died_array['year'] );
					}
				}
				// Get the post slug
				$post_slug = get_post_field( 'post_name', get_post( $dead_char ) );
				$death_list_array[ $post_slug ] = array(
					'name'  => get_the_title( $dead_char ),
					'url'   => get_the_permalink( $dead_char ),
					'shows' => $show_info,
					'died'  => $died,
				);
			}

			// phpcs:disable
			// Reorder all the dead to sort by DoD
			uasort( $death_list_array, function( $a, $b ) {
				// Spaceship doesn't work
				// $return = $a['died'] <=> $b['died'];
				$return = '0';
				if ( $a['died'] < $b['died'] ) {
					$return = '-1';
				}
				if ( $a['died'] > $b['died'] ) {
					$return = '1';
				}
				return $return;
			});
			// phpcs:enable
			?>
			<ul>
				<?php
				foreach ( $death_list_array as $dead ) {
					echo '<li><a href="' . esc_url( $dead['url'] ) . '">' . esc_html( $dead['name'] ) . '</a> / ' . wp_kses_post( $dead['shows'] ) . ' / ' . esc_html( date( 'd F', $dead['died'] ) ) . ' </li>';
				}
				?>
			</ul>
			<?php
		} else {
			?>
			<p>No known characters died in <?php echo (int) $thisyear; ?>.</p>
			<?php
		}
		wp_reset_query();
	}
	/**
	 * List of shows for the year.
	 *
	 * @access public
	 * @param mixed $thisyear
	 * @return void
	 */
	public function shows( $thisyear ) {
		$thisyear = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		// Constants
		$shows_this_year = array(
			'current' => 0,
			'ended'   => 0,
			'started' => 0,
		);
		$shows_current   = array();
		$shows_started   = array();
		$shows_ended     = array();
		$shows_queery = LWTV_Loops::post_type_query( 'post_type_shows' );
		if ( $shows_queery->have_posts() ) {
			while ( $shows_queery->have_posts() ) {
				$shows_queery->the_post();
				$show_id   = get_the_ID();
				$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
				$show_name = strtolower( $show_name );
				// Shows Currently Airing
				if ( get_post_meta( $show_id, 'lezshows_airdates', true ) ) {
					$airdates  = get_post_meta( $show_id, 'lezshows_airdates', true );
					$countries = get_the_term_list( $show_id, 'lez_country' );
					$formats   = get_the_term_list( $show_id, 'lez_formats' );
					if (
						( 'current' === $airdates['finish'] && date( 'Y' ) === $thisyear ) // Still Current and it's NOW
						|| ( $airdates['finish'] >= $thisyear && $airdates['start'] <= $thisyear ) // Airdates between
					) {
						// Currently Airing Shows shows for the current year only
						$shows_current[ $show_name ] = array(
							'url'     => get_permalink( $show_id ),
							'name'    => get_the_title( $show_id ),
							'status'  => get_post_status( $show_id ),
							'country' => wp_strip_all_tags( $countries ),
							'format'  => wp_strip_all_tags( $formats ),
						);
						$shows_this_year['current']++;
					}
					// Shows that ended this year
					if ( $airdates['finish'] === $thisyear ) {
						$shows_ended[ $show_name ] = array(
							'url'     => get_permalink( $show_id ),
							'name'    => get_the_title( $show_id ),
							'status'  => get_post_status( $show_id ),
							'country' => wp_strip_all_tags( $countries ),
							'format'  => wp_strip_all_tags( $formats ),
						);
						$shows_this_year['ended']++;
					}
					// Shows that STARTED this year
					if ( $airdates['start'] === $thisyear ) {
						$shows_started[ $show_name ] = array(
							'url'     => get_permalink( $show_id ),
							'name'    => get_the_title( $show_id ),
							'status'  => get_post_status( $show_id ),
							'country' => wp_strip_all_tags( $countries ),
							'format'  => wp_strip_all_tags( $formats ),
						);
						$shows_this_year['started']++;
					}
				}
			}
		}
		?>

		<hr>

		<h3><a name="showsonair">Shows Aired This Year (<?php echo (int) $shows_this_year['current']; ?>)</a></h3>

		<?php
		if ( 0 !== $shows_this_year['current'] ) {
			echo '<ul class="this-year-shows showsonair">';
			foreach ( $shows_current as $show ) {
				$show_output = $show['name'];
				if ( 'publish' === $show['status'] ) {
					$show_output = '<a href="' . $show['url'] . '">' . $show['name'] . '</a> <small>(' . $show['country'] . ' - ' . $show['format'] . ')</small>';
				}
				echo '<li>' . wp_kses_post( $show_output ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>No qualifying shows aired in ' . (int) $thisyear . '.</p>';
		}
		?>

		<hr>

		<h3><a name="showsstart">Shows That Started This Year (<?php echo (int) $shows_this_year['started']; ?>)</a></h3>

		<?php
		if ( 0 !== $shows_this_year['started'] ) {
			echo '<ul class="this-year-shows showsstart">';
			foreach ( $shows_started as $show ) {
				$show_output = $show['name'];
				if ( 'publish' === $show['status'] ) {
					$show_output = '<a href="' . $show['url'] . '">' . $show['name'] . '</a> <small>(' . $show['country'] . ' - ' . $show['format'] . ')</small>';
				}
				echo '<li>' . wp_kses_post( $show_output ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>No qualifying shows aired in ' . (int) $thisyear . '.</p>';
		}
		?>

		<hr>

		<h3><a name="showsend">Shows That Ended This Year (<?php echo (int) $shows_this_year['ended']; ?>)</a></h3>

		<?php
		if ( 0 !== $shows_this_year['ended'] ) {
			echo '<ul class="this-year-shows showsend">';
			foreach ( $shows_ended as $show ) {
				$show_output = $show['name'];
				if ( 'publish' === $show['status'] ) {
					$show_output = '<a href="' . $show['url'] . '">' . $show['name'] . '</a> <small>(' . $show['country'] . ' - ' . $show['format'] . ')</small>';
				}
				echo '<li>' . wp_kses_post( $show_output ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>No qualifying shows aired in ' . (int) $thisyear . '.</p>';
		}
		wp_reset_query();
	}
	/**
	 * Navigation for the year
	 * @param  [type]  $thisyear
	 * @return boolean           [description]
	 */
	public static function navigation( $thisyear ) {
		$thisyear = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		$lastyear = FIRST_LWTV_YEAR;
		$baseurl  = '/this-year/';
		?>

		<nav aria-label="This Year navigation" role="navigation">
			<ul class="pagination justify-content-center">

				<?php
				// If it's not 1961, we can show the first year we have queers
				if ( $thisyear !== $lastyear ) {
					?>
					<li class="page-item first mr-auto"><a href="<?php echo esc_url( $baseurl . $lastyear . '/' ); ?>" class="page-link"><?php echo lwtv_yikes_symbolicons( 'caret-left-circle.svg', 'fa-chevron-circle-left' ); ?> First (<?php echo (int) $lastyear; ?>)</a></li>
					<li class="page-item previous"><a href="<?php echo esc_url( $baseurl . ( $thisyear - 1 ) . '/' ); ?>" title="previous year" class="page-link"><?php echo lwtv_yikes_symbolicons( 'caret-left.svg', 'fa-chevron-left' ); ?> Previous</a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $thisyear - 2 ) . '/' ); ?>" class="page-link"><?php echo (int) ( $thisyear - 2 ); ?></a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $thisyear - 1 ) . '/' ); ?>" class="page-link"><?php echo (int) ( $thisyear - 1 ); ?></a></li>
					<?php
				}
				?>

				<li class="page-item active"><span class="active page-link"><?php echo (int) $thisyear; ?></span></li>

				<?php
				if ( date( 'Y' ) !== $thisyear ) {
					?>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $thisyear + 1 ) . '/' ); ?>" class="page-link"><?php echo (int) ( $thisyear + 1 ); ?></a></li>
					<li class="page-item next"><a href="<?php echo esc_url( $baseurl . ( $thisyear + 1 ) . '/' ); ?>" class="page-link" title="next year">Next <?php echo lwtv_yikes_symbolicons( 'caret-right-circle.svg', 'fa-chevron-circle-right' ); ?></a></li>
					<li class="page-item last ml-auto"><a href="<?php echo esc_url( $baseurl . date( 'Y' ) . '/' ); ?>" class="page-link">Last (<?php echo (int) date( 'Y' ); ?>)<?php echo lwtv_yikes_symbolicons( 'caret-right.svg', 'fa-chevron-right' ); ?></a></li>
					<?php
				}
				?>
			</ul>
		</nav><!-- .navigation -->
		<?php
	}


}

new LWTV_This_Year();
