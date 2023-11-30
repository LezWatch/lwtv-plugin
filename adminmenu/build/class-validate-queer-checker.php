<?php
/*
 * Validation: Queer Checks For LezWatch.TV
 *
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_AdminMenu_Validate_Queer_Checker_Build {
	/**
	 * Output the results of queer checking...
	 */
	public static function make() {

		$items = LWTV_Features_Transients::get_transient( 'lwtv_debug_queercheck' );

		// If rerun was clicked, gotta check 'em all.
		if ( ( isset( $_POST['rerun'] ) && check_admin_referer( 'run_queer_checker_clicked' ) ) || false === $items ) {
			$items = ( new LWTV_Debugger_Queers() )->find_queerchars();
		}

		// If recheck was clicked, only check the problem children.
		if ( isset( $_POST['recheck'] ) && check_admin_referer( 'run_queer_checker_clicked' ) && false !== $items ) {
			$items = ( new LWTV_Debugger_Queers() )->find_queerchars( $items );
		}

		// Get the last run time.
		$last_run = LWTV_AdminMenu_Validation::last_run( 'queercheck' );

		// Defaults.
		$button  = 'Run Scan';
		$is_name = 'rerun';

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>Every character's queerness matches their actors.</p>
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
			$have = _n( 'character', 'characters', $count );
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo (int) $count; ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following <?php echo esc_html( $have ); ?> your attention. Please edit the actor or character queerness as indicated.</p>
					<?php echo wp_kses_post( $last_run ); ?>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Character</th>
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
		<form action="admin.php?page=lwtv_data_check&tab=queer_checker" method="post">
			<?php wp_nonce_field( 'run_queer_checker_clicked' ); ?>
			<input type="hidden" value="true" name=<?php echo esc_attr( $is_name ); ?> />
			<?php submit_button( $button ); ?>
		</form>
		<?php
	}
}
