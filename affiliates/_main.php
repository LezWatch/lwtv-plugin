<?php
/**
 * Name: Affiliate Code
 * Description: Automagical affiliate things
 */

// Require Widgets code
require_once 'widget.php';

class LWTV_Affilliates {

	// These define the possible layouts and affiliates
	public static $valid_types;
	public static $valid_formats;

	/**
	 * __construct function.
	 */
	public function __construct() {

		self::$valid_types   = array( 'random' );
		self::$valid_formats = array( 'text', 'thin', 'tiny', 'wide' );

		add_filter( 'widget_text', 'do_shortcode' );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_footer', array( $this, 'apple_auto_link_maker' ) );
		add_filter( 'the_content', array( $this, 'amazon_publisher_studio' ), 11 );
		add_action( 'wp_head', array( $this, 'add_meta_tags' ), 2 );
	}

	/**
	 * Init
	 */
	public function init() {
		add_shortcode( 'affiliates', array( $this, 'shortcode_affiliates' ) );
	}

	/**
	 * Add Meta Tags for Affiliates
	 */
	public function add_meta_tags() {
		// https://impact.com
		echo '<meta name="ir-site-verification-token" value="1145177634" />';
	}

	/*
	 * Display Affiliate Ads
	 * Usage: [affiliates]
	 * @since 1.0
	*/
	public function shortcode_affiliates( $atts ) {
		if ( is_archive() ) {
			$affiliates = $this->random( 'random', 'thin' );
		} else {
			$affiliates = $this->random( 'random', 'wide' );
		}

		$advert = '<!-- BEGIN Affiliate Ads --><center>' . $affiliates . '</center><!-- END Affiliate Ads -->';

		return $advert;
	}

	/**
	 * Call something random...
	 * This is a basic check of a random number
	 */
	public function random( $id, $format ) {
		$format = ( in_array( $format, self::$valid_formats, true ) ) ? esc_attr( $format ) : 'wide';

		switch ( $format ) {
			case 'banner':
				$advert = adrotate_group( 1 );
				break;
			case 'text':
				$advert = adrotate_group( 5 );
				break;
			case 'thin':
				$advert = adrotate_group( 3 );
				break;
			case 'tiny':
				$advert = adrotate_group( 4 );
				break;
			case 'wide':
				$advert = adrotate_group( 2 );
				break;
		}

		return '<div class="aa-random">' . $advert . '</div>';
	}

	/**
	 * Insert Apple's Auto Link Maker
	 * https://autolinkmaker.itunes.apple.com/?at=1010lMaT
	 */
	public function apple_auto_link_maker() {
		echo "<script type='text/javascript'>var _merchantSettings=_merchantSettings || [];_merchantSettings.push(['AT', '1010lMaT']);(function(){var autolink=document.createElement('script');autolink.type='text/javascript';autolink.async=true; autolink.src= ('https:' == document.location.protocol) ? 'https://autolinkmaker.itunes.apple.com/js/itunes_autolinkmaker.js' : 'http://autolinkmaker.itunes.apple.com/js/itunes_autolinkmaker.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(autolink, s);})();</script>";
	}

	/**
	 * Edit Amazon URLs to have our tag at the end.
	 * @param  [string] $text [URL]
	 * @return [string]       [URL with our tag added]
	 */
	public function amazon_publisher_studio( $text ) {
		$regex_url = '#<a href="(?:https://(?:www\.){0,1}amazon\.com(?:/.*){0,1}(?:/dp/|/gp/product/))(.*?)(?:/.*|$)"#';

		if ( preg_match( $regex_url, $text, $url ) ) {
			$linkurl = rtrim( $url[0], '"' );
			$content = preg_replace( $regex_url, $linkurl . '?tag=lezpress-20"', $text );
		} else {
			$content = $text;
		}

		return $content;
	}

	/**
	 * Determine what to call for actors
	 * This is just random
	 */
	public function actors( $id, $format ) {
		$affiliates = $this->random( $id, $format );
		$advert     = '<!-- BEGIN Affiliate Ads --><div class="affiliate-ads"><center>' . $affiliates . '</center></div><!-- END Affiliate Ads -->';
		return $advert;
	}

	/**
	 * Determine what to call for characters
	 * This is just random
	 */
	public function characters( $id, $format ) {
		$affiliates = $this->random( $id, $format );
		$advert     = '<!-- BEGIN Affiliate Ads --><div class="affiliate-ads"><center>' . $affiliates . '</center></div><!-- END Affiliate Ads -->';
		return $advert;
	}

	/**
	 * Determine what to call for shows
	 * This is much more complex!
	 */
	public function shows( $id, $format ) {

		$advert = '';

		// Show a different show ad depending on things...
		if ( 'affiliate' === $format ) {
			// Show an affiliate link
			require_once 'ways-to-watch.php';
			$advert = ( new LWTV_Ways_To_Watch() )->affiliate_link( $id );
		} else {
			$affiliates = $this->random( $id, $format );
			$advert     = '<!-- BEGIN Affiliate Ads --><div class="affiliate-ads"><center>' . $affiliates . '</center></div><!-- END Affiliate Ads -->';
		}

		return $advert;
	}

}

new LWTV_Affilliates();
