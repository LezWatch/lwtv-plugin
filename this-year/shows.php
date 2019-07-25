<?php
/**
 * Shows for The Year
 *
 * @package LezWatch.TV
 */

class LWTV_This_Year_Shows {

	public static function toc() {
		?>
		<section id="toc" class="toc-container card-body">
			<nav class="breadcrumb">
				<h4 class="toc-title">Go to:</h4>
				<a class="breadcrumb-item smoothscroll" href="#showsonair">Shows On Air</a>
				<a class="breadcrumb-item smoothscroll" href="#showsstart">Shows That Began</a>
				<a class="breadcrumb-item smoothscroll" href="#showsend">Shows That Ended</a>
			</nav>
		</section>
		<?php
	}

	/**
	 * List of shows for the year.
	 *
	 * @access public
	 * @param mixed $thisyear
	 * @return void
	 */
	public function list( $thisyear ) {
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
		$shows_queery    = LWTV_Loops::post_type_query( 'post_type_shows' );
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
}

new LWTV_This_Year_Shows();
