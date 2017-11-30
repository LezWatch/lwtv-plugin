<?php
/**
 * Name: Twitter
 * Description: Connects to Twitter and does THINGS
 */

if ( ! defined('WPINC' ) ) die;

// If the file can't be found, bail.
if ( !file_exists( WP_CONTENT_DIR . '/library/assets/twitter/vendor/autoload.php' ) || !defined( 'TWITTER_CONSUMER_KEY' ) || !defined( 'TWITTER_CONSUMER_SECRET' )  ) return;

include_once( WP_CONTENT_DIR . '/library/assets/twitter/vendor/autoload.php' );
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * class LWTV_Twitter
 */

class LWTV_Twitter {

	/**
	 * Constuctor.
	 */
	public function __construct( ) {
		add_action( 'wp_dashboard_setup',  array( $this, 'add_dashboard_widgets' ) );
	}

	/**
	 * Add Dashboard Widget
	 */
	public function add_dashboard_widgets() {
		wp_add_dashboard_widget(
			'lwtv_twitter_dashboard_widget',            // Widget slug.
			'Today\'s Character & Show',                  // Title.
			array( $this, 'dashboard_widget_function' ) // Display function.
		);
	}

	/**
	 * Output Dashboard Widget
	 */
	public function dashboard_widget_function( $post, $callback_args ) {
		$char = LWTV_OTD_JSON::of_the_day( 'character' );
		$show = LWTV_OTD_JSON::of_the_day( 'show' );
		
		echo '<ul>
				<li><strong>Character:</strong> ' . $char['name'] . '</li>
				<li><strong>Show:</strong> ' . $show['name'] . '</li>
			</ul>';
	}

	/*
	 * Twitter Connection
	 */
	public function twitter_shit( $type ) {
		
		$valid_types = array ( 'character', 'show' );
		if ( !isset( $type ) || !in_array( $type, $valid_types ) ) return;

		$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $access_token, $access_token_secret);
		$content = $connection->get("account/verify_credentials");

		$data = LWTV_OTD_JSON::of_the_day( $type );
		$tweet = 'The ' . $type . ' of the day is ' . $data['name'] . ' - ' . $data['url'];

	}


}
new LWTV_Twitter();