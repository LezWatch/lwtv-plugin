<?php

class LWTV_This_Year_Dead_Format {
	/**
	 * Output the dead
	 *
	 * @param  int  $this_year Year to look for
	 *
	 * @return void (echos output)
	 */
	public function make( $this_year, $count = false, $char_array = array() ) {
		$this_year  = ( isset( $this_year ) ) ? $this_year : gmdate( 'Y' );
		$count      = ( isset( $char_array['count'] ) ) ? $char_array['count'] : '0';
		$list_array = ( isset( $char_array['list'] ) ) ? $char_array['list'] : '';
		$show_array = ( isset( $char_array['show'] ) ) ? $char_array['show'] : '';
		?>
		<h2><a name="died"><?php echo (int) $count; ?> Characters Died</a></h2>

		<p>&nbsp;</p>

		<?php
		if ( ! empty( $list_array ) ) {
			?>
			<div class="container">
				<div class="row">
					<div class="col">
						<h3>By Date</h3>

						<table class="table table-md table-hover table-striped">
							<thead class="thead-dark">
								<tr>
									<th style="width: 150px;" scope="col">Date</th>
									<th scope="col">Character(s)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( $list_array as $date => $chars ) {
									echo '<tr>';
									echo '<td>' . esc_html( gmdate( 'd F', $date ) ) . ' (' . count( $chars ) . ')</td>';
									echo '<td><ul>';
									foreach ( $chars as $char ) {
										echo '<li><a href="' . esc_url( $char['url'] ) . '">' . esc_html( $char['name'] ) . '</a> - ' . wp_kses_post( $char['shows'] ) . '</li>';
									}
									echo '</ul></td>';
									echo '</tr>';
								}
								?>
							</tbody>
						</table>
					</div>
					<div class="col">
						<h3>By Show</h3>
						<table class="table table-md table-hover table-striped">
							<thead class="thead-dark">
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
			echo '<p>No known characters died in ' . (int) $this_year . '.</p>';
		}
	}
}
