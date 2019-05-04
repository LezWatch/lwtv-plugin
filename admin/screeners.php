<?php
/*
 * Code to show screeners page
 *
 * @since 2.3
*/

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Screeners {

	/**
	 * make_readable function.
	 *
	 * @access public
	 * @param mixed $bytes
	 * @return void
	 */
	public static function make_readable( $bytes ) {
		$i = floor( log( $bytes, 1024 ) );
		return round( $bytes / pow( 1024, $i ), [ 0, 0, 2, 2, 3 ][ $i ] ) . [ 'B', 'kB', 'MB', 'GB', 'TB' ][ $i ];
	}

	/*
	 * Settings Page Content
	 *
	 * A list of all the fucking screeners.
	 */
	public static function settings_page() {

		// phpcs:ignore WordPress.Security.NonceVerification
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'intro';

		// For what it's worth, I would never do this anywhere else, but I'm lazy
		echo '<style>.collapse { display: none; }.collapse.show { display: block; }</style>';
		wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/inc/bootstrap/js/bootstrap.min.js', array( 'jquery' ), '4.0.0', 'all', true );
		?>
		<div class="wrap">
			<h1>Screeners and Downloads</h1>

			<h2 class="nav-tab-wrapper">
				<a href="?page=screeners" class="nav-tab <?php echo ( 'intro' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Introduction</a>
				<a  href="?page=screeners&tab=videos" class="nav-tab <?php echo ( 'videos' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Videos</a>
				<a  href="?page=screeners&tab=networks" class="nav-tab <?php echo ( 'networks' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Networks</a>
			</h2>

			<?php
			switch ( $active_tab ) {
				case 'videos':
					self::tab_videos();
					break;
				case 'networks':
					self::tab_networks();
					break;
				default:
					self::tab_introduction();
			}
			?>
		</div>
		<?php
	}

	/**
	 * Static Introduction to what the hell is going on...
	 */
	public static function tab_introduction() {
		?>
		<h4>Videos</h4>

		<p>Downloadable videos for anything Tracy asked for.</p>

		<h4>Networks</h4>

		<p>Links to networks we have media access with. This includes screeners and photographs.</p>
		<?php
	}

	/**
	 * The list of networks we have access to
	 */
	public static function tab_networks() {
		$networks = array(
			'AMC (including BBC America)'      => 'http://press.amcnetworks.com/',
			'Disney/ABC'                       => 'https://www.disneyabcpress.com',
			'NBC (includes USA and Telemundo)' => 'https://www.nbcumv.com',
			'Starz'                            => 'https://mediaroom.starz.com',
			'Tello Films'                      => 'https://tellofilms.com',
		);
		?>

		<p>The following sites are ones we have media access to. Please contact Mika or Tracy if you need access (handled via 1Password). <em>REMEMBER</em>! Under pain of death, we cannot share this information with anyone. You can use the images for pages here, but you <em>MUST</em> credit them appropriately.</p>

		<table class="widefat fixed" cellspacing="0">
			<thead><tr>
				<th id="network" class="manage-column column-network" scope="col">Network</th>
				<th id="url" class="manage-column column-url" scope="col">URL</th>
			</tr></thead>

			<tbody>
				<?php
				$number = 1;
				foreach ( $networks as $network => $url ) {
					$class = ( 0 === $number % 2 ) ? '' : 'class="alternate"';
					echo '
					<tr ' . esc_attr( $class ) . '>
						<td><a href="' . esc_url( $url ) . '" target="_new">' . esc_html( $network ) . '</a></td>
						<td>' . esc_url( $url ) . '</td>
					</tr>
					';
					$number++;
				}
				?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * All the videos for Tracy
	 */
	public static function tab_videos() {

		// Check for the AWS functions
		if ( ! function_exists( 'Aws\constantly' ) ) {
			echo '<p>Cannot reach Amazon at this time.</p>';
		} else {
			// Establish connection with AWS S3
			$client = new Aws\S3\S3Client(
				[
					'version'     => '2006-03-01',
					'region'      => 'us-west-2',
					'credentials' => [
						'key'    => AMAZON_PRODUCT_API_KEY,
						'secret' => AMAZON_PRODUCT_API_SECRET,
					],
				]
			);

			$bucket  = 'lezwatch';
			$objects = $client->listObjectsV2(
				[
					'Bucket' => $bucket,
					'Prefix' => 'screeners/',
				]
			);

			?>
			<p>Videos you can download and watch and damn the man!</p>
				<ul>
					<?php
					foreach ( $objects['Contents'] as $object ) {

						echo '<li>';

						if ( '/' !== substr( $object['Key'], -1 ) ) {
							$plain_url  = $client->getObjectUrl( $bucket, $object['Key'] );
							$cmd        = $client->getCommand(
								'GetObject',
								[
									'Bucket' => $bucket,
									'Key'    => $object['Key'],
								]
							);
							$signed_url = $client->createPresignedRequest( $cmd, '+1 hour' );
							$time_good  = date( 'H:i', current_time( 'timestamp' ) + 60 * 60 );
							$size       = self::make_readable( $object['Size'] );
								echo '<li>&bull; <a href="' . esc_url( $signed_url->getUri() ) . '" download>' . esc_html( $object['Key'] ) . '</a> - ' . esc_html( $size ) . ' (Link valid until ' . esc_html( $time_good ) . ' ET)</li>';
						}

						echo '</li>';
					}
					?>
				</ul>
			<?php
		}
	}

}

new LWTV_Screeners();
