<?php
/**
 * Name: Display Content
 * Description: Dynamically generated content, called by themes
 */

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Shows_Display
 *
 * @since 1.0
 */

class LWTV_Shows_Display {

	/**
	 * Echo content warning if needed.
	 *
	 * @access public
	 * @return void
	 */
	public static function echo_content_warning( $type = 'full' ) {
		switch ( get_post_meta( get_the_ID(), 'lezshows_triggerwarning', true ) ) {
			case "on":
				$warning = '<strong>WARNING!</strong> This show contains scenes of explicit violence, drug use, suicide, sex, and/or abuse.';
				break;
			case "med":
				$warning = '<strong>CAUTION!</strong> This show regularly discusses and sometimes depicts "strong content" like violence and abuse.';
				break;
			case "low":
				$warning = '<strong>NOTICE!</strong> While generally acceptable for the over 14 crowd, this show may hit some sensitive topics now and then.';
				break;
			default:
				$warning = 'none';
		}

		$hand_image = '⚠';
		if ( $type !== 'amp' && defined( 'LP_SYMBOLICONS_PATH' ) ) {
			$hand_request = wp_remote_get( LP_SYMBOLICONS_PATH.'hand.svg' );
			$hand_image = '<span role="img" aria-label="Warning Hand" title="Warning Hand">' . $hand_request['body'] .'</span>';
		}

		if ( $warning !== 'none' ) {
			echo '<div class="callout callout-trigger-' . get_post_meta( get_the_ID(), 'lezshows_triggerwarning', true ) . '">' . $hand_image . '<p>' . $warning . ' If those aren\'t your speed, neither is this show.
			</p></div>';
		}
	}

	/**
	 * display_worthit function.
	 *
	 * @access public
	 * @param string $show_id
	 * @param string $thumb_rating (default: 'meh')
	 * @return void
	 */
	public static function display_worthit( $show_id, $thumb_rating = 'Meh' ) {

		// Bail if not set
		if ( $thumb_rating == null ) return '<p><em>Coming soon...</em></p>';

		?>
		<div class="ratings-icons">
			<div class="worthit worthit-<?php echo esc_attr( $thumb_rating ); ?>">
				<?php
				if ( $thumb_rating == "Yes" ) { $thumb_icon = "thumbs_up.svg"; }
				if ( $thumb_rating == "Meh" ) { $thumb_icon = "meh-o.svg"; }
				if ( $thumb_rating == "No" )  { $thumb_icon = "thumbs_down.svg"; }

				$thumb_image = '';
				if ( defined( 'LP_SYMBOLICONS_PATH' ) ) {
					$thumb_request = wp_remote_get( LP_SYMBOLICONS_PATH . '' . $thumb_icon );
					$thumb_image   = $thumb_request['body'];
				}

				echo '<span role="img" class="show-worthit ' . lcfirst( $thumb_rating ) . '">' . $thumb_image . '</span>';
				echo get_post_meta( $show_id, 'lezshows_worthit_rating', true );
				?>
			</div>
		</div>

		<div class="ratings-details">
			<?php
				if ( ( get_post_meta( $show_id, 'lezshows_worthit_details', true ) ) ) {
					echo apply_filters( 'the_content', wp_kses_post( get_post_meta( $show_id, 'lezshows_worthit_details', true ) ) );
				}
			?>

			<ul class="network-data">
				<?php
				$stations = get_the_terms( $show_id, 'lez_stations' );
				if ( $stations && ! is_wp_error( $stations ) ) {
					echo '<li class="network names">'. get_the_term_list( $show_id, 'lez_stations', '<strong>Airs On:</strong> ', ', ' ) .'</li>';
				}
				$formats = get_the_terms( $show_id, 'lez_formats' );
				if ( $formats && ! is_wp_error( $formats ) ) {
					echo '<li class="network formats">'. get_the_term_list( $show_id, 'lez_formats', '<strong>Show Format:</strong> ', ', ' ) .'</li>';
				}
				if ( get_post_meta($show_id, 'lezshows_airdates', true) ) {
					$airdates = get_post_meta( $show_id, 'lezshows_airdates', true );
					echo '<li class="network airdates"><strong>Airdates:</strong> '. $airdates['start'] .' - '. $airdates['finish'] .'</li>';
				}
				?>
			</ul>

		</div>
		<?php
	}

	/**
	 * display_tropes function.
	 *
	 * @access public
	 * @param mixed $show_id
	 * @return void
	 */
	public static function display_tropes( $show_id ) {

		// get the tropes associated with this show
		$terms = get_the_terms( $show_id, 'lez_tropes' );
		// if tropes are found, and no errors are returned
		if ( $terms && ! is_wp_error( $terms ) ) {
			?><ul class="trope-list"><?php
				// loop over each returned trope
				foreach( $terms as $term ) { ?>
					<li class="show trope trope-<?php echo $term->slug; ?>">
						<a href="<?php echo get_term_link( $term->slug, 'lez_tropes'); ?>" rel="show trope"><?php
							// Make sure Symbolicons exist
							$taxicon = $term->name;
							if ( defined( 'LP_SYMBOLICONS_PATH' ) ) {
								$icon = get_term_meta( $term->term_id, 'lez_termsmeta_icon', true );
								$svg  = wp_remote_get( LP_SYMBOLICONS_PATH.'' . $icon . '.svg' );

								if ( empty( $icon ) || $svg['response']['code'] !== '404' ) {
									$taxicon = $svg['body'];
								}
							}
							echo $taxicon;
						?></a>
						<a href="<?php echo get_term_link( $term->slug, 'lez_tropes'); ?>" rel="show trope" class="trope-link"><?php
							echo $term->name;
						?></a>
					</li><?php
				}
			?></ul><?php
		} else {
			echo '<p><em>Coming soon...</em></p>';
		}
	}

	/**
	 * display_hearts function.
	 *
	 * @access public
	 * @param mixed $show_id
	 * @param string $realness (default: '0')
	 * @param string $quality (default: '0')
	 * @param string $screentime (default: '0')
	 * @return void
	 */
	public static function display_hearts( $show_id, $realness = '0', $quality = '0', $screentime = '0' ) {

		// Bail if not set
		if ( $realness == '0' && $quality == '0' && $screentime == '0' ) return '<p><em>Coming soon...</em></p>';

		$heart_types = array( 'realness', 'quality', 'screentime' );

		$heart = '♥';
		if ( defined( 'LP_SYMBOLICONS_PATH' ) ) {
			$heart_request = wp_remote_get( LP_SYMBOLICONS_PATH.'heart.svg' );
			$heart         = $heart_request['body'];
		}

		$positive_heart = '<span role="img" class="show-heart positive">' . $heart . '</span>';
		$negative_heart = '<span role="img" class="show-heart negative">' . $heart . '</span>';

		foreach ( $heart_types as $type ) {

			switch ( $type ) {
				case 'realness';
					$rating = $realness;
					$detail = 'lezshows_realness_details';
					break;
				case 'quality';
					$rating = $quality;
					$detail = 'lezshows_quality_details';
					break;
				case 'screentime';
					$rating = $screentime;
					$detail = 'lezshows_screentime_details';
					break;
			}

			if ( $rating > '0' ) {
				?>
				<div class="ratings-icons">
					<h3><?php echo ucfirst( $type ); ?></h3>
					<?php
					// while loop to display filled in hearts
					// based on set ratings
					$i = 1;
					while( $i <= $rating ) {
						echo $positive_heart;
						$i++;
					}
					// calculate the remaining empty hearts
					if ( $i >= 1 ) {
						$loop_count = $i - 1;
					} else {
						$loop_count = 0;
					}
					while ( $loop_count < 5 ) {
						echo $negative_heart;
						$loop_count++;
					}
					?><span class="screen-reader-text">Rating: <?php echo $rating ?> Hearts (out of 5)</span>
				</div>
				<?php

				if( ( get_post_meta( $show_id, $detail, true) ) ) {
					echo apply_filters( 'the_content', wp_kses_post( get_post_meta( $show_id, $detail, true ) ) );
				}
			}
		}
	}
	
}

new LWTV_Shows_Display();