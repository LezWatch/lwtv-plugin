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

	/*
	 * Construct
	 *
	 * Actions to happen immediately
	 */
	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	/*
	 * Init
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
	}

	/*
	 * Settings
	 *
	 * Create our settings page
	 */
	public function add_settings_page() {
		add_submenu_page( 'lwtv_tools', 'Screeners', 'Screeners', 'edit_posts', 'screeners', array( $this, 'settings_page' ) );
	}

	/**
	 * make_readable function.
	 *
	 * @access public
	 * @param mixed $bytes
	 * @return void
	 */
	public function make_readable( $bytes ) {
		$i = floor( log( $bytes, 1024 ) );
		return round( $bytes / pow( 1024, $i ), [ 0, 0, 2, 2, 3 ][ $i ] ) . [ 'B', 'kB', 'MB', 'GB', 'TB' ][ $i ];
	}

	/*
	 * Settings Page Content
	 *
	 * A list of all the fucking screeners.
	 */
	public function settings_page() {

		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'intro'; // WPCS: CSRF ok.

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
	public function tab_networks() {
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
	public function tab_videos() {

		// If the file can't be found, bail.
		if ( ! file_exists( WP_CONTENT_DIR . '/library/assets/aws/aws-autoloader.php' ) ) {
			return;
		}

		// Call the AWS SDK
		require_once WP_CONTENT_DIR . '/library/assets/aws/aws-autoloader.php';

		// Establish connection with DreamObjects with an S3 client.
		$client = new Aws\S3\S3Client([
			'version'     => '2006-03-01',
			'region'      => 'us-east-1',
			'endpoint'    => 'https://objects-us-east-1.dream.io',
			'credentials' => [
				'key'    => DHO_ACCESS_KEY_ID,
				'secret' => DHO_SECRET_ACCESS_KEY,
			],
		]);

		$bucket  = 'lezpress-screeners';
		$objects = $client->listObjectsV2( [ 'Bucket' => $bucket ] );

		?>
		<p>Videos you can download and watch and damn the man!</p>
			<ul>
				<?php
				foreach ( $objects['Contents'] as $object ) {

					echo '<li>';

					if ( '/' === substr( $object['Key'], -1 ) ) {
						$name = rtrim( $object['Key'], '/' );
						echo '<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#' . esc_attr( strtolower( $name ) ) . '">' . esc_attr( $name ) . '</button>';
						echo '<div id="' . esc_attr( strtolower( $name ) ) . '" class="collapse"><ul>';

						$subobjects = $client->listObjectsV2([
							'Bucket'     => $bucket,
							'Prefix'     => $object['Key'],
							'StartAfter' => $object['Key'],
						]);

						foreach ( $subobjects['Contents'] as $subobject ) {
							if ( substr( $subobject['Key'], -1 ) !== '/' ) {
								$plain_url  = $client->getObjectUrl( $bucket, $subobject['Key'] );
								$cmd        = $client->getCommand('GetObject', [
									'Bucket' => $bucket,
									'Key'    => $subobject['Key'],
								]);
								$signed_url = $client->createPresignedRequest( $cmd, '+1 hour' );
								$size       = self::make_readable( $subobject['Size'] );
								echo '<li>&bull; <a href="' . esc_url( $signed_url->getUri() ) . '" download>' . esc_html( $subobject['Key'] ) . '</a> - ' . esc_html( $size ) . '</li>';
							}
						}
						echo '</ul></div>';
					}

					echo '</li>';
				}
				?>
			</ul>
		<?php

	}

}

new LWTV_Screeners();
