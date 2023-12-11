<?php

namespace LWTV\Statistics\Format;

class Lists {
	/*
	 * Statistics Display Lists
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * @param string $subject The content subject
	 * @param string $slug    The data 'subject' - used to generate the URLs
	 * @param array  $data   The array of data
	 * @param string $count   The count of posts
	 *
	 * @return Content
	 */
	public function make( $subject, $slug = null, $data = array(), $count = 0 ) {
		?>
		<table id="<?php echo esc_html( $subject ); ?>Table" class="tablesorter table table-striped table-hover">
			<thead>
				<tr>
					<th scope="col">&nbsp;</th>
					<th scope="col">Count (<?php echo (int) $count; ?>)</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $data as $item ) {
					$name = ( 'Dead Lesbians (Dead Queers)' === $item['name'] ) ? 'Dead' : $item['name'];
					if ( 0 !== $item['count'] ) {
						echo '<tr>';
							echo '<th scope="row"><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $name ) . '</a></th>';
							echo '<td>' . (int) $item['count'] . '</td>';
						echo '</tr>';
					}
				}
				?>
			</tbody>
		</table>
		<?php
	}
}
