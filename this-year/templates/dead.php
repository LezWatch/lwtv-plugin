<?php
/**
 * The template for displaying the dead of the year
 *
 * @package LezWatch.TV
 */

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
