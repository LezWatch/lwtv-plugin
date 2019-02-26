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
	public static $format_sizes;

	/**
	 * __construct function.
	 */
	public function __construct() {

		self::$valid_types   = array( 'random', 'cbs', 'amazon' );
		self::$valid_formats = array( 'banner', 'text', 'thin', 'tiny', 'wide' );
		self::$format_sizes  = array(
			'affiliate' => '',
			'banner'    => '728x90',
			'text'      => '',
			'thin'      => '160x600',
			'tiny'      => '320x50',
			'wide'      => '300x250',
		);

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
	public static function shortcode_affiliates( $atts ) {
		if ( is_archive() ) {
			$affiliates = $this->widget( 'random', 'thin' );
		} else {
			$affiliates = $this->widget( 'random', 'wide' );
		}

		$advert = '<!-- BEGIN Affiliate Ads --><div class="affiliate-ads"><center>' . $affiliates . '</center></div><!-- END Affiliate Ads -->';

		return $advert;
	}

	/**
	 * Build the widget
	 *
	 * @access public
	 * @return array
	 */
	public static function widget( $type, $format ) {

		$format = ( in_array( $format, self::$valid_formats, true ) ) ? esc_attr( $format ) : 'wide';
		$id     = get_the_ID();

		switch ( $type ) {
			case 'amazon':
				$advert = self::amazon( $id, $format );
				break;
			case 'amc':
				$advert = self::network( $id, $format, 'amc' );
				break;
			case 'apple':
				$advert = self::apple( $id, $format );
				break;
			case 'cbs':
				$advert = self::network( $id, $format, 'cbs' );
				break;
			case 'local':
				$advert = self::local( $id, $format );
				break;
			default:
				$advert = self::random( $id, $format );
		}

		return $advert;
	}

	/**
	 * Call something random...
	 * This is a basic check of a random number
	 */
	public static function random( $id, $format ) {
		$format = ( in_array( $format, self::$valid_formats, true ) ) ? esc_attr( $format ) : 'wide';
		$number = wp_rand();
		if ( 0 === $number % 3 ) {
			$advert = self::network( $id, $format, 'amc' );
		} elseif ( 0 === $number % 2 ) {
			$advert = self::network( $id, $format, 'cbs' );
		} else {
			$advert = self::amazon( $id, $format );
		}
		return $advert;
	}

	/**
	 * Call Amazon Affilate Data
	 */
	public static function amazon( $id, $format ) {
		require_once 'amazon.php';
		return LWTV_Affiliate_Amazon::show_ads( $id, $format );
	}

	/**
	 * Call Apple Affiliate Data
	 */
	public static function apple( $id, $format ) {
		require_once 'apple.php';
		return LWTV_Affiliate_Apple::show_ads( $id, $format );
	}

	/**
	 * Network Channels
	 * @param  int    $id       Post ID
	 * @param  string $format   Type of Add (default WIDE)
	 * @param  array  $network  Network to be called (default CBS)
	 * @return string           The ad
	 */
	public static function network( $id, $format = 'wide', $network = 'cbs' ) {

		switch ( $network ) {
			case 'amc':
				require_once 'cj.php';
				$advert = LWTV_Affiliate_CJ::show_ads( $id, 'amc', $format );
				break;
			case 'cbs':
				require_once 'cbs.php';
				$advert = LWTV_Affiliate_CBS::show_ads( $id, $format );
				break;
			case 'starz':
				require_once 'cj.php';
				$advert = LWTV_Affiliate_CJ::show_ads( $id, 'starz', $format );
				break;
		}

		return $advert;
	}


	/**
	 * Call Local Affilate Data
	 */
	public static function local( $id, $format = 'wide' ) {
		require_once 'local.php';
		return LWTV_Affiliate_Local::show_ads( $id, $format );
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
	public static function amazon_publisher_studio( $text ) {
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
	public static function actors( $id, $format ) {
		$return = self::random( $id, $format );
		return $return;
	}

	/**
	 * Determine what to call for characters
	 * This is just random
	 */
	public static function characters( $id, $format ) {
		$affiliates = self::random( $id, $format );
		$advert     = '<!-- BEGIN Affiliate Ads --><div class="affiliate-ads"><center>' . $affiliates . '</center></div><!-- END Affiliate Ads -->';
		return $advert;
	}

	/**
	 * Determine what to call for shows
	 * This is much more complex!
	 */
	public static function shows( $id, $format ) {

		// Show a different show ad depending on things...
		if ( 'affiliate' === $format ) {
			// Show an affiliate link
			$affiliates = self::affiliate_link( $id );
		} else {
			// Show an advert
			$format     = ( in_array( $format, self::$valid_formats, true ) ) ? $format : 'wide';
			$is_special = self::get_special_stations( $id );

			// If it's a special station, we'll show that, else show random
			if ( $is_special['cbs'] ) {
				// CBS pays best.
				$get_the_ad = self::network( $id, $format, 'cbs' );
			} elseif ( $is_special['amc'] ) {
				$get_the_ad = self::network( $id, $format, 'amc' );
			} elseif ( $is_special['starz'] ) {
				$get_the_ad = self::network( $id, $format, 'starz' );
			} else {
				$get_the_ad = self::random( $id, $format );
			}

			$affiliates = '<div class="affiliate-ads"><center>' . $get_the_ad . '</center></div>';
		}

		$advert = '<!-- BEGIN Affiliate Links -->' . $affiliates . '<!-- END Affiliate Links -->';

		return $advert;
	}

	/**
	 * Get the stations and kick back a simple true/falsey
	 * @param  int    $post_id Post ID
	 * @return array           Array of special status
	 */
	public static function get_special_stations( $post_id ) {
		$slug             = get_post_field( 'post_name', $post_id );
		$stations         = get_the_terms( $post_id, 'lez_stations' );
		$is_special       = array(
			'amc'      => false,
			'bbc'      => false,
			'cbs'      => false,
			'showtime' => false,
			'starz'    => false,
		);
		$special_stations = array(
			'amc'      => array( 'amc', 'sundance', 'shudder', 'bbc-america', 'ifc' ),
			'bbc'      => array( 'bbc-four', 'bbc-one', 'bbc-three', 'bbc-two', 'bbc-wales', 'cbbc' ),
			'cbs'      => array( 'cbs', 'cbs-all-access', 'cw', 'the-cw', 'cw-seed', 'upn', 'wb' ),
			'showtime' => array( 'showtime' ),
			'starz'    => array( 'starz' ),
		);

		// Convert stations into a simple array of slugs.
		// We prioritize CBS because they pay the best.
		if ( $stations && ! is_wp_error( $stations ) ) {
			foreach ( $stations as $station ) {
				if ( in_array( $station->slug, $special_stations['cbs'], true ) ) {
					$is_special['cbs'] = true;
				} elseif ( in_array( $station->slug, $special_stations['amc'], true ) ) {
					$is_special['amc'] = true;
				} elseif ( in_array( $station->slug, $special_stations['starz'], true ) ) {
					$is_special['starz'] = true;
				}
			}
		}

		// CBS is always true for Star Trek so let's double check.
		if ( strpos( $slug, 'star-trek' ) !== false ) {
			$is_special['cbs'] = true;
		}

		return $is_special;
	}

	/**
	 * Call Custom Affiliate Links
	 * This is used by shows to figure out where people can watch things
	 * There's some juggling for certain sites
	 */
	public static function affiliate_link( $id ) {

		$affiliate_url = get_post_meta( $id, 'lezshows_affiliate', true );
		$links         = array();

		// Parse each URL to figure out who it is...
		foreach ( $affiliate_url as $url ) {
			$parsed_url = wp_parse_url( $url );
			$hostname   = $parsed_url['host'];
			$clean_url  = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $parsed_url['path'];

			// Clean the URL to get the top domain ...
			$removal_array = array( 'www.', '.com', 'itunes.', '.co.uk' );
			foreach ( $removal_array as $removal ) {
				$hostname = str_replace( $removal, '', $hostname );
			}

			// Lets get the URLs!
			switch ( $hostname ) {
				case 'amazon':
					$url   = $clean_url . 'ref=as_li_tl?ie=UTF8&tag=lezpress-20';
					$name  = 'Amazon';
					$extra = '<img src="//ir-na.amazon-adsystem.com/e/ir?t=lezpress-20&l=pf4&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />';
					break;
				case 'apple':
				case 'itunes':
					$url  = $clean_url . '?mt=4&at=1010lMaT';
					$name = 'iTunes';
					break;
				case '7eer':
				case 'cbs':
					$cbs_id = self::network( $id, 'id', 'cbs' );
					$url    = 'https://cbs-allaccess.7eer.net/c/1242493/' . $cbs_id . '/3065';
					$extra  = '<img height="0" width="0" src="//cbs-allaccess.7eer.net/c/1242493/' . $cbs_id . '/3065" style="position:absolute;visibility:hidden;" border="0" />';
					$name   = 'CBS All Access';
					break;
				case 'abc':
				case 'nbc':
					$name = strtoupper( $hostname );
					break;
				case 'abc.go':
					$name = 'ABC';
					break;
				case 'bbcamerica':
					$name = 'BBC America';
					break;
				case 'cwtv':
					$name = 'The CW';
					break;
				case 'youtube':
					$name = 'YouTube';
					break;
				case 'tellofilms':
					$name = 'Tello Films';
					break;
				case 'cartoonnetwork':
					$name = 'Cartoon Network';
					break;
				case 'showtimeanytime':
					$name = 'Showtime';
					break;
				default:
					$name = ucfirst( $hostname );
			}

			$extra   = ( isset( $extra ) ) ? $extra : '';
			$links[] = '<a href="' . $url . '" target="_blank" class="btn btn-primary" rel="nofollow">' . $name . '</a>' . $extra;
		}

		$link_output = implode( $links, '' );

		$icon   = lwtv_yikes_symbolicons( 'tv-hd.svg', 'fa-tv' );
		$output = $icon . '<span class="how-to-watch">Ways to Watch:</span> ' . $link_output;

		return $output;
	}

}

new LWTV_Affilliates();
