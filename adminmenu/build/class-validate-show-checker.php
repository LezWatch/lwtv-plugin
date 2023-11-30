<?php
/*
 * Validation: Show Checks For LezWatch.TV
 *
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_AdminMenu_Validate_Show_Checker_Build {
	/**
	 * Output the results of Show checking...
	 */
	public static function tab_show_checker() {

		$items = LWTV_Features_Transients::get_transient( 'lwtv_debug_show_problems' );

		// If rerun was clicked, gotta check 'em all.
		if ( ( isset( $_POST['rerun'] ) && check_admin_referer( 'run_show_checker_clicked' ) ) || false === $items ) {
			$items = ( new LWTV_Debugger_Shows() )->find_shows_problems();
		}

		// If recheck was clicked, only check the problem children.
		if ( isset( $_POST['recheck'] ) && check_admin_referer( 'run_show_checker_clicked' ) && false !== $items ) {
			$items = ( new LWTV_Debugger_Shows() )->find_shows_problems( $items );
		}

		// Get the last run time.
		$last_run = LWTV_AdminMenu_Validation::last_run( 'show_problems' );

		// Default.
		$button  = 'Run Scan';
		$is_name = 'rerun';

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>All shows look good and the data looks sane.</p>
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
			$have = _n( 'show needs', 'shows need', $count );
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo (int) $count; ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following <?php echo esc_html( $have ); ?> your attention.</p>
					<p>Note: Remember that intersectionality is meant to be a <em>positive</em> representation. If it's bad disability rep (like Grey's Anatomy with Arizona), do not list them.</p>
					<?php echo wp_kses_post( $last_run ); ?>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Show</th>
						<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
						<th id="date" class="manage-column column-date" scope="col">Last Updated</th>
					</tr></thead>

					<tbody>
						<?php
						LWTV_AdminMenu_Validation::table_content( $items );
						?>
					</tbody>
				</table>
			</div>
			<?php
		}

		?>
		<form action="admin.php?page=lwtv_data_check&tab=show_checker" method="post">
			<?php wp_nonce_field( 'run_show_checker_clicked' ); ?>
			<input type="hidden" value="true" name=<?php echo esc_attr( $is_name ); ?> />
			<?php submit_button( $button ); ?>
		</form>
		<?php
	}
}
