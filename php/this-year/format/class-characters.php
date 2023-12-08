<?php

namespace LWTV\This_Year\Format;

class Characters {
	/**
	 * List of shows for the year.
	 *
	 * @access public
	 * @param string $this_year
	 *
	 * @return void
	 */
	public function make( $this_year, $count, $build_data ) {
		$this_year  = ( isset( $this_year ) ) ? $this_year : gmdate( 'Y' );
		$loop_array = $build_data;
		$char_array = $loop_array['list'];
		$show_array = $loop_array['show'];
		?>

		<h2><?php echo (int) $loop_array['count']; ?> Characters On Air</h2>

		<p>&nbsp;</p>

		<?php
		// If the data isn't empty, we go!
		if ( ! empty( $char_array ) && ! empty( $show_array ) ) {

			// List everyone by character
			?>
			<div class="container">
				<div class="row">
					<div class="col">
						<h3>By Character Name</h3>
						<table class="table table-md table-hover table-striped">
							<thead class="thead-light">
								<tr>
									<th scope="col">Name</th>
									<th scope="col">Show(s)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( $char_array as $the_char ) {
									echo '<tr>';
									echo '<td><a href="' . esc_url( $the_char['url'] ) . '">' . esc_html( $the_char['name'] ) . '</a></td>';
									echo '<td>' . wp_kses_post( $the_char['shows'] ) . '</td>';
									echo '</tr>';
								}
								?>
							</tbody>
						</table>
					</div>
					<div class="col">
						<h3>By Show</h3>

						<table class="table table-md table-hover table-striped">
							<thead class="thead-light">
								<tr>
									<th style="width: 200px;" scope="col">Show</th>
									<th scope="col">Character(s)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( $show_array as $show ) {
									echo '<tr><td><em><a href="' . esc_url( $show['url'] ) . '">' . esc_html( $show['name'] ) . '</a></em> (' . count( $show['chars'] ) . ')';
									echo '<br /><small>(' . wp_kses_post( $show['country'] ) . ' - ' . wp_kses_post( $show['format'] ) . ')</small></td><td><ul>';
									foreach ( $show['chars'] as $char ) {
										echo '<li><a href="' . esc_url( $char['url'] ) . '">' . esc_html( $char['name'] ) . '</a> <small>(' . esc_html( $char['type'] ) . ' character)</small></li>';
									}
									echo '</ul></td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<?php
		} else {
			echo '<p>No characters were on air in ' . (int) $this_year . '.</p>';
		}
	}
}
