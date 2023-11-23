<?php
/*
 * Monitors
 *
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Admin_Monitors {

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

		$table = array(
			'tvmaze'   => self::check_tvmaze(),
			'gtmetrix' => self::check_gtmetrix(),
			'uptime'   => self::check_uptime_robot( 'lwtv' ),
		);
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
								<?php
								$row_count = 1;
								foreach ( $table as $row ) {
									$class = ( 0 === ( $row_count % 2 ) ) ? 'alternate' : '';
									echo '<tr class="' . esc_attr( $class ) . '" />';
										echo '<td>' . wp_kses_post( $row['description'] ) . '</td>';
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo '<td><div class="monitor"><div class="monitor icon">' . $row['icon'] . '</div><div class="monitor status">' . wp_kses_post( $row['status'] ) . '</div></td>';
									echo '</tr>';
									++$row_count;
								}
								?>
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
			$status .= '<em>ERROR! The TVMaze calendar file is missing! Tell Mika.<em>';
		} else {
			$file_time  = filemtime( $filename );
			$time_since = human_time_diff( $file_time, strtotime( time() ) );

			// If the time is less than 24 hours, we're good.
			// If it's under 48 hours, it's a warning.
			// If it's over that, it's an error.

			$icon = array(
				'symbolicon'   => 'smile.svg',
				'font-awesome' => 'fa-face-smile',
				'class'        => 'success',
			);
			if ( $file_time <= strtotime( '-48 hours' ) ) {
				$icon = array(
					'symbolicon'   => 'frown.svg',
					'font-awesome' => 'fa-face-frown',
					'class'        => 'failure',
				);
			} elseif ( $file_time <= strtotime( '-36 hours' ) ) {
				$icon = array(
					'symbolicon'   => 'meh.svg',
					'font-awesome' => 'fa-face-meh',
					'class'        => 'warning',
				);
			}

			$status = '<p><strong>Last updated:</strong> ' . wp_date( 'D, d M Y H:i:s', $file_time, new DateTimeZone( 'America/Los_Angeles' ) ) . ' (' . $time_since . ' ago).</p>';
		}

		$return = array(
			'description' => '<strong><a href="https://tvmaze.com" target="_new">TVMaze</a></strong><br/>Local ICS file, updates daily.',
			'icon'        => ( new LWTV_Functions() )->symbolicons( $icon['symbolicon'], $icon['font-awesome'], 'symbolicons ' . $icon['class'] ),
			'status'      => $status,
		);

		return $return;
	}

	public static function check_gtmetrix() {

		$gtmetrix_url = 'https://gtmetrix.com/api/2.0/pages/Mtpr9uKg/latest-report';
		$icon         = array(
			'symbolicon'   => 'warning.svg',
			'font-awesome' => 'fa-triangle-exclamation',
			'class'        => 'warning',
		);

		if ( ! defined( 'LWTV_GTMETRIX' ) ) {
			$status = '<em>ERROR! The GTMetrix API key has not been added to this site. Tell Mika.<em>';
		} else {
			$status = LWTV_Features_Transients::get_transient( 'lwtv_gtmetrix' );

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
				$status   = '<strong>Grade</strong>: ' . $report['gtmetrix_grade'] . '<br/><strong>Performance</strong>: ' . $report['performance_score'] . '<br/><strong>Structure</strong>: ' . $report['structure_score'];

				set_transient( 'lwtv_gtmetrix', $status, ( DAY_IN_SECONDS * 2 ) );
				set_transient( 'lwtv_gtmetrix_grade', $report['gtmetrix_grade'], ( DAY_IN_SECONDS * 2 ) );
			}
		}

		$grade = LWTV_Features_Transients::get_transient( 'lwtv_gtmetrix_grade' );
		if ( isset( $grade ) ) {
			switch ( $grade ) {
				case 'A':
					$icon = array(
						'symbolicon'   => 'thumbs-up.svg',
						'font-awesome' => 'fa-thumbs-up',
						'class'        => 'success',
					);
					break;
				case 'B':
					$icon = array(
						'symbolicon'   => 'thumbs-up.svg',
						'font-awesome' => 'fa-thumbs-up',
						'class'        => 'warning',
					);
					break;
				case 'C':
					$icon = array(
						'symbolicon'   => 'thumbs-up.svg',
						'font-awesome' => 'fa-thumbs-up',
						'class'        => 'failure',
					);
					break;
				case 'D':
					$icon = array(
						'symbolicon'   => 'thumbs-down.svg',
						'font-awesome' => 'fa-thumbs-down',
						'class'        => 'warning',
					);
					break;
				case 'F':
					$icon = array(
						'symbolicon'   => 'thumbs-down.svg',
						'font-awesome' => 'fa-thumbs-down',
						'class'        => 'failure',
					);
					break;
			}
		}

		$return = array(
			'description' => '<strong><a href="https://gtmetrix.com" target="_new">GTMetrix</a></strong><br/>Remote service, updates scores daily.',
			'icon'        => ( new LWTV_Functions() )->symbolicons( $icon['symbolicon'], $icon['font-awesome'], 'symbolicons ' . $icon['class'] ),
			'status'      => $status,
		);

		return $return;
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
			$icon      = array(
				'symbolicon'   => 'mug-heart.svg',
				'font-awesome' => 'fa-circle',
				'class'        => 'success',
			);

			if ( ! empty( $body['monitors'][0] ) ) {
				$response       = $body['monitors'][0];
				$monitor_status = (int) $response['status'];
				$monitor_uptime = $response['all_time_uptime_ratio'];
				$monitor_uptime = number_format( $monitor_uptime, 2 );
				$website_name   = $response['friendly_name'];

				switch ( $monitor_status ) {
					case '0':
						$status        = 'Monitoring is currently <em>paused</em>. This may be for website updates/maintenance. Please check again later for an updated status report.';
						$icon['class'] = 'warning';
						break;
					case '9':
						if ( defined( 'LWTV_DEV_SITE' ) && LWTV_DEV_SITE ) {
							$duration      = get_date_from_gmt( ( time() - $response['logs'][0]['duration'] ), 'Y-m-d H:i:s' );
							$status        = 'The monitor reports that this site is down. The only reason you see this message is you are using a dev site.<br /> ' . $website_name . ' has been down for ' . $duration . '.';
							$icon['class'] = 'failure';

						} else {
							$status        = 'How are you here?';
							$icon['class'] = 'failure';
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
							$last_downtime = gmdate( 'jS F Y', $response['logs'][1]['datetime'] );
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

		$return = array(
			'description' => '<strong><a href="https://uptimerobot.com" target="_new">Uptime Robot</a> / <a href="https://status.lezwatchtv.com" target="_new">status.lezwatchtv.com</a></strong><br/>Remote service, tracks uptime live.',
			'icon'        => ( new LWTV_Functions() )->symbolicons( $icon['symbolicon'], $icon['font-awesome'], 'symbolicons ' . $icon['class'] ),
			'status'      => $status,
		);

		return $return;
	}
}

new LWTV_Admin_Monitors();
