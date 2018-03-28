<?php
/*
Description: Screeners and all that jazz
Version: 1.0
Author: Mika Epstein
*/

// if this file is called directly abort
if ( ! defined('WPINC' ) ) {
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
	 *
	 * Actions to happen on WP init
	 * - add settings page
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
		$page = add_management_page( 'Videos', 'Videos', 'edit_posts', 'videos', array( $this, 'settings_page' ) );
	}


	/**
	 * MakeReadable function.
	 * 
	 * @access public
	 * @param mixed $bytes
	 * @return void
	 */
	public function MakeReadable($bytes) {
		$i = floor(log($bytes, 1024));
		return round($bytes / pow(1024, $i), [0,0,2,2,3][$i]).['B','kB','MB','GB','TB'][$i];
	}

	/*
	 * Settings Page Content
	 *
	 * A list of all the fucking screeners.
	 */
	function settings_page() {

		// We need this...
		require 'assets/aws.phar';

		// Establish connection with DreamObjects with an S3 client.
		$client = new Aws\S3\S3Client([
			'version'     => '2006-03-01',
			'region'      => 'us-east-1',
			'endpoint'    => 'https://objects-us-east-1.dream.io',
				'credentials' => [
				'key'      => DHO_ACCESS_KEY_ID,
				'secret'   => DHO_SECRET_ACCESS_KEY,
			]
		]);

		$bucket  = 'lezpress-screeners';
		$objects = $client->listObjectsV2([
			'Bucket' => $bucket,
		]);

		?>
		<div class="wrap">
			
			<h2>Videos</h2>
			
			<p>Videos you can download and watch and damn the man!</p>
			
			<ul>
				<?php
				foreach ( $objects['Contents'] as $object ){

					echo '<li>';

					if ( substr( $object['Key'], -1 ) == '/' ) {
						echo '<br /><strong>' . $object['Key'] . '</strong>';
					} else {
						$plain_url = $client->getObjectUrl( $bucket, $object['Key'] );
						$cmd = $client->getCommand('GetObject', [
							'Bucket' => $bucket,
							'Key'    => $object['Key'],
						]);
						$signed_url = $client->createPresignedRequest($cmd, '+1 hour');
						$size       = self::MakeReadable( $object['Size'] );
						echo '&bull; <a href="' . $signed_url->getUri() .'" download>' . $object['Key'] . '</a> - ' . $size;
					}

					echo '</li>';
				}
		?>
			</ul>
		</div>
		<?php
	}
}

new LWTV_Screeners();