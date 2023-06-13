<?php
/*
 * Monitors
 *
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Monitor_Checks {

	/*
	 * Construct
	 *
	 * Actions to happen immediately
	 */
	public function __construct() {
		//Null
	}

	/*
	 * Settings Page Content
	 */
	public static function settings_page() {
		?>
		<div class="wrap">

			<h1>Monitors</h1>

			<div id="dashboard" class="lwtvtab">
				<div class="lwtv-tools-container">
					<h3>LezWatch.TV Service Monitors</h3>
					<p>Sometimes we have to monitor things.</p>

					<p>Everything here runs on it's own, but we should keep up with them.</p>

					<div class="lwtv-tools-table">
						<table class="widefat fixed" cellspacing="0">
							<thead><tr>
								<th id="service" class="manage-column column-service" scope="col">Service</th>
								<th id="status" class="manage-column column-status" scope="col">Status</th>
							</tr></thead>
							<tbody>
								<tr>
									<td><strong><a href="https://tvmaze.com" target="_new">TVMaze</a></strong><br/>Local ICS file, updates daily.</td>
									<td><?php self::check_tvmaze(); ?></td>
								</tr>
								<tr class="alternate">
									<td><strong><a href="https://gtmetrix.com" target="_new">GTMetrix</a></strong><br/>Remote service, updates scores daily.</td>
									<td><?php self::check_gtmetrix(); ?></td>
								</tr>
								<tr>
									<td><strong><a href="https://uptimerobot.com" target="_new">Uptime Robot</a> / <a href="https://status.lezwatchtv.com" target="_new">status.lezwatchtv.com</a></strong><br/>Remote service, tracks uptime live.</td>
									<td><?php self::check_uptime_robot( 'lwtv' ); ?></td>
								</tr>
							</tbody>
						</table>
					</div>

					<p>If there are any other tools we should be monitoring, let Mika know!</p>
				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * Check TVMaze
	 *
	 * Returns the file/date stamp of the file, so we know if it's been updated.
	 */
	public static function check_tvmaze() {
		$upload_dir = wp_upload_dir();
		$filename   = $upload_dir['basedir'] . '/tvmaze.ics';

		if ( ! file_exists( $filename ) ) {
			$status = '<em>ERROR! The TVMaze calendar file is missing! Tell Mika.<em>';
		} else {
			$file_time = filemtime( $filename );
			$status    = '<strong>Last updated:</strong> ' . gmdate( 'F d Y H:i:s.', $file_time );
		}

		// ToDo: If there has been more than 48 hours without an update, make some noise.

		echo wp_kses_post( $status );
	}

	public static function check_gtmetrix() {

		$gtmetrix_url = 'https://gtmetrix.com/api/2.0/pages/Mtpr9uKg/latest-report';

		if ( ! defined( 'LWTV_GTMETRIX' ) ) {
			$status = '<em>ERROR! The GTMetrix API key has not been added to this site. Tell Mika.<em>';
		} else {
			$status = LWTV_Transients::get_transient( 'lwtv_gtmetrix' );

			// If there is no status, we run the API check. Every OTHER day.
			if ( false === $status || empty( $status ) ) {
				$args = array(
					'headers' => array(
						'Authorization' => 'Basic ' . base64_encode( LWTV_GTMETRIX . ':' ),
					),
				);

				$response = wp_remote_request( $gtmetrix_url, $args );
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$report   = $body['data']['attributes'];

				$status  = '<strong>Grade</strong>: ' . $report['gtmetrix_grade'];
				$status .= '<br/><strong>Performance</strong>: ' . $report['performance_score'];
				$status .= '<br/><strong>Structure</strong>: ' . $report['structure_score'];

				set_transient( 'lwtv_gtmetrix', $status, ( DAY_IN_SECONDS * 2 ) );
			}
		}

		echo wp_kses_post( $status );

	}

	public static function check_uptime_robot( $sitename ) {
		$uptime_url = 'https://api.uptimerobot.com/v2/getMonitors';
		$api_keys   = array(
			'lwtv'      => 'm781800598-b7f0dce51e9c3b7d9d85e200',
			'lwtv-docs' => 'm783186644-93e4a02249e1011bac3c4923',
		);

		if ( ! isset( $api_keys[ $sitename ] ) ) {
			$status = '<em>ERROR! The sitename (' . sanitize_text_field( $sitename ) . ') is not monitored at this time.';
		} else {
			$api_key = $api_keys[ $sitename ];
			$request = 'api_key=' . $api_key . '&format=json&logs=1&log_types=1&logs_limit=1&all_time_uptime_ratio=1';

			$args = array(
				'body' => array(
					'api_key'               => $api_key,
					'format'                => 'json',
					'logs'                  => '1',
					'log_limit'             => '1',
					'all_time_uptime_ratio' => '1',
				),
			);

			$curl_call = wp_remote_post( $uptime_url, $args );
			$body      = json_decode( wp_remote_retrieve_body( $curl_call ), true );

			if ( ! empty( $body['monitors'][0] ) ) {
				$response       = $body['monitors'][0];
				$monitor_status = (int) $response['status'];
				$monitor_uptime = $response['all_time_uptime_ratio'];
				$monitor_uptime = number_format( $monitor_uptime, 2 );
				$website_name   = $response['friendly_name'];

				switch ( $monitor_status ) {
					case '0':
						$status = 'Monitoring is currently <em>paused</em>. This may be for website updates/maintenance. Please check again later for an updated status report.';
						break;
					case '9':
						if ( defined( 'LWTV_DEV_SITE' ) && LWTV_DEV_SITE ) {
							$status = 'The monitor reports that this site is down. The only reason you see this message is you are using a dev site.';

							$last_duration = time() - $response['logs'][0]['duration'];
							$duration      = get_date_from_gmt( $last_duration, 'Y-m-d H:i:s' );
							$status .= '<br /> ' . $website_name . ' has been down for ' . $duration . '.';

						} else {
							$status = 'How are you here?';
						}
						break;
					case '2':
					default:
						$status = '<strong>Monitoring is active and operational.</strong>';

						// Check if there has been any recorded downtime:
						if ( empty( $response['logs'] ) ) {
							// No downtime recorded:
							$status .= '<br />There has been no recorded downtime. Ever.';
						} else {
							// Date and time since last downtime:
							$last_downtime = date( 'jS F Y', $response['logs'][1]['datetime'] );
							$time_since    = human_time_diff( $response['logs'][1]['datetime'], time() );

							// Duration of last downtime:
							$duration = human_time_diff( time(), ( time() - $response['logs'][1]['duration'] ) );

							// Output:
							$status .= '<br />It has been ' . $time_since . ' since the last recorded downtime (' . $last_downtime . ') which lasted ' . $duration . '.';
						}

						break;
				}
			}
		}

		echo wp_kses_post( $status );

	}

}

new LWTV_Monitor_Checks();
