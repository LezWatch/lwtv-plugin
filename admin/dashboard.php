<?php
/*
 * Dashboard Widget
 *
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Tools_Dashboard {

	/*
	 * Construct
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'custom_dashboard_widgets' ) );
	}

	public function custom_dashboard_widgets() {
		if ( current_user_can( 'upload_files' ) ) {
			wp_add_dashboard_widget( 'lwtv_tools_widget', 'LezWatch.TV Tools', array( $this, 'lwtv_tools_dashboard_content' ) );
		}
	}

	public function lwtv_tools_dashboard_content() {
		// Get Last Status
		$options   = get_option( 'lwtv_debugger_status' );
		$timestamp = $options['timestamp'];
		unset( $options['timestamp'] );
		?>
		<div class="main">
			<p>The tools were last run on <strong><?php echo esc_html( get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), 'F j, Y H:i:s' ) ); ?></strong>.</p>

			<p><strong>Current Status</strong></p>

			<ul>
				<?php
				$output = '';
				foreach ( $options as $an_option ) {
					if ( $an_option['count'] > 0 ) {
						$output .= '<li>&bull; ' . $an_option['name'] . ' - ' . $an_option['count'] . '</li>';
					}
				}

				if ( empty( $output ) ) {
					$output = '<p>No issues found! Celebrate gay times!</p>';
				}

				echo wp_kses_post( $output );

				?>
			</ul>

			<a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=lwtv_tools' ) ); ?>" class="button-secondary">Go to the Tools Dashboard</a>
		</div>

		<?php

	}

}

new LWTV_Tools_Dashboard();
