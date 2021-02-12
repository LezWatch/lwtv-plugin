<?php
/**
 * Name: Affiliate Code
 * Description: Automagical affiliate things
 *
 * Links:
 * https://appleservices-console.partnerize.com/v2/overview/overview
 * https://affiliate.itunes.apple.com/resources/documentation/basic_affiliate_link_guidelines_for_the_phg_network/
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

		self::$valid_types   = array( 'random', 'cbs', 'amazon', 'fubutv' );
		self::$valid_formats = array( 'banner', 'text', 'thin', 'tiny', 'wide' );

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
			$affiliates = $this->widget( 'random', 'thin' );
		} else {
			$affiliates = $this->widget( 'random', 'wide' );
		}

		$advert = '<!-- BEGIN Affiliate Ads --><center>' . $affiliates . '</center><!-- END Affiliate Ads -->';

		return $advert;
	}

	/**
	 * Build the widget
	 *
	 * @access public
	 * @return array
	 */
	public function widget( $type, $format ) {

		$format = ( in_array( $format, self::$valid_formats, true ) ) ? esc_attr( $format ) : 'wide';
		$id     = get_the_ID();

		switch ( $type ) {
			case 'amazon':
				$advert = '<!-- Amazon -->' . $this->amazon( $id, $format );
				break;
			case 'apple':
				$advert = '<!-- Apple -->' . $this->apple( $id, $format );
				break;
			case 'cbs':
				$advert = '<!-- CBS -->' . $this->network( $id, $format, 'cbs' );
				break;
			case 'fubutv':
				$advert = '<!-- FubuTV -->' . $this->fubutv( $id, $format );
				break;
			case 'local':
				$advert = '<!-- Local/Affiliate -->' . $this->local( $id, $format );
				break;
			default:
				$advert = '<!-- Random -->' . $this->random( $id, $format );
		}

		return '<div class="affiliate-ads">' . $advert . '</div>';
	}

	/**
	 * Call something random...
	 * This is a basic check of a random number
	 */
	public function random( $id, $format ) {
		$format  = ( in_array( $format, self::$valid_formats, true ) ) ? esc_attr( $format ) : 'wide';
		$choices = array(
			'cbs'    => '<!-- CBS -->' . $this->network( $id, $format, 'cbs' ),
			'fubutv' => '<!-- FubuTV -->' . $this->fubutv( $id, $format ),
			'amazon' => '<!-- Amazon -->' . $this->amazon( $id, $format ),
		);
		$random  = array_rand( $choices );
		$advert  = $choices[ $random ];

		return $advert;
	}

	/**
	 * Call Amazon Affiliate Data
	 */
	public function amazon( $id, $format ) {
		require_once 'amazon.php';
		return ( new LWTV_Affiliate_Amazon() )->show_ads( $id, $format );
	}

	/**
	 * Call FubuTV Affiliate Data
	 */
	public function fubutv( $id, $format ) {
		require_once 'fubutv.php';
		return ( new LWTV_Affiliate_FubuTV() )->show_ads( $id, $format );
	}

	/**
	 * Network Channels
	 * @param  int    $id       Post ID
	 * @param  string $format   Type of Add (default WIDE)
	 * @param  array  $network  Network to be called (default CBS)
	 * @return string           The ad
	 */
	public function network( $id, $format = 'wide', $network = 'cbs' ) {

		$advert = '';
		switch ( $network ) {
			case 'cbs':
				require_once 'cbs.php';
				$advert = ( new LWTV_Affiliate_CBS() )->show_ads( $id, $format );
				break;
		}

		return $advert;
	}

	/**
	 * Call Local Affiliate Data
	 */
	public function local( $id, $format = 'wide' ) {
		require_once 'local.php';
		return ( new LWTV_Affiliate_Local() )->show_ads( $id, $format );
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

		// Show a different show ad depending on things...
		if ( 'affiliate' === $format ) {
			// Show an affiliate link
			require_once( 'ways-to-watch.php' );
			$affiliates = ( new LWTV_Ways_To_Watch() )->affiliate_link( $id );
		} else {
			// Show an advert
			$format     = ( in_array( $format, self::$valid_formats, true ) ) ? $format : 'wide';
			$is_special = $this->get_special_stations( $id );

			// If it's a special station, we'll show that, else show random
			if ( $is_special['cbs'] ) {
				// CBS pays best.
				$get_the_ad = $this->network( $id, $format, 'cbs' );
			} else {
				$get_the_ad = $this->random( $id, $format );
			}

			$affiliates = '<div class="affiliate-ads"><center>' . $get_the_ad . '</center></div>';
		}

		$advert = '<!-- BEGIN Affiliate Links -->' . $affiliates . '<!-- END Affiliate Links -->';

		return $advert;
	}

	/**
	 * Get the stations and kick back a simple true/falsey
	 * @param  int    $post_id Post ID
	 * @return array  Array of special status
	 *
	 * A special status is for stations that belong to someone bigger.
	 * Think like how The CW is owned by CBS. They share ads so we share
	 * ads.
	 */
	public function get_special_stations( $post_id ) {
		$slug             = get_post_field( 'post_name', $post_id );
		$stations         = get_the_terms( $post_id, 'lez_stations' );
		$is_special       = array(
			'cbs'      => false,
		);
		$special_stations = array(
			'cbs'      => array( 'cbs', 'cbs-all-access', 'cw', 'the-cw', 'cw-seed', 'upn', 'wb', 'showtime', 'paramount-network', 'paramount-comedy', 'nick-com', 'nickelodeon', 'teen-nick', 'nick-jr' ),
		);

		//
		// We're going to convert them into a simpler list.
		if ( $stations && ! is_wp_error( $stations ) ) {
			// Since we have stations, we will loop through the stations on the show.
			foreach ( $stations as $station ) {
				// Loop through all the special stations.
				foreach ( $special_stations as $special_station => $special_value ) {
					// If the special station is NOT already true (remember shows can have multiple stations)
					// AND the show is in the array for the special station (i.e. Legends is on CW which is CBS)
					// then we want to set the station to true:
					if ( false === $is_special[ $special_station ] && in_array( $station->slug, $special_stations[ $special_station ], true ) ) {
						$is_special[ $special_station ] = true;
					}
				}
			}
		}

		// CBS is always true for Star Trek so let's double check.
		if ( strpos( $slug, 'star-trek' ) !== false ) {
			$is_special['cbs'] = true;
		}

		return $is_special;
	}

}

new LWTV_Affilliates();
