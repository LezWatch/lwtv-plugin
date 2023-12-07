<?php
/*
 * Validation: Empty Actor Checks For LezWatch.TV
 *
 */

namespace LWTV\Validator;

use LWTV\Admin_Menu\Validation;

class Actor_Empty {
	/**
	 * Output the results of actors with missing data ...
	 */
	public static function make() {

		$items = lwtv_plugin()->get_transient( 'lwtv_debug_actor_empty' );

		// If re-run, do a whole full scan!
		if ( ( isset( $_POST['rerun'] ) && check_admin_referer( 'run_actor_incomplete_clicked' ) ) || false === $items ) {
			$items = lwtv_plugin()->find_actors_incomplete();
		}

		// If recheck was clicked, only check the problem children.
		if ( isset( $_POST['recheck'] ) && check_admin_referer( 'run_actor_incomplete_clicked' ) && false !== $items ) {
			$items = lwtv_plugin()->find_actors_incomplete( $items );
		}

		// Get the last run time.
		$last_run = ( new Validation() )->last_run( 'actor_empty' );

		// Default.
		$button  = 'Run Scan';
		$is_name = 'rerun';

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>All actors have at least some information.</p>
					<?php echo wp_kses_post( $last_run ); ?>
				</div>
			</div>
			<?php
		} elseif ( false === $items ) {
			$button  = 'Full Scan';
			$is_name = 'rerun';
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-dissmiss"></span> Bogus!</h3>
				<div id="lwtv-tools-alerts">
					<p>Something has gone wrong. Please run a full scan. If this repeats, let Mika know.</p>
					<?php echo wp_kses_post( $last_run ); ?>
				</div>
			</div>
			<?php
		} else {
			$button  = 'Recheck';
			$is_name = 'recheck';

			$count = count( $items );
			// translators: %s is the number of shows.
			$have = _n( 'actor is', 'actors are', $count );
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo (int) $count; ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following <?php echo esc_html( $have ); ?> missing critical data. Please review and update as needed.</p>
					<?php echo wp_kses_post( $last_run ); ?>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Actor</th>
						<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
						<th id="date" class="manage-column column-date" scope="col">Last Updated</th>
					</tr></thead>

					<tbody>
						<?php ( new Validation() )->table_content( $items ); ?>
					</tbody>
				</table>
			</div>
			<?php
		}

		?>
		<form action="admin.php?page=lwtv_data_check&tab=actor_empty" method="post">
			<?php wp_nonce_field( 'run_actor_incomplete_clicked' ); ?>
			<input type="hidden" value="true" name=<?php echo esc_attr( $is_name ); ?> />
			<?php submit_button( $button ); ?>
		</form>
		<?php
	}
}
