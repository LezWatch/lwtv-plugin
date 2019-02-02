<?php
/**
 * Name: Affiliate Code
 * Description: Automagical affiliate things
 */

class LWTV_Affilliates {

	/**
	 * __construct function.
	 */
	public function __construct() {
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
		add_shortcode( 'amazon-bounties', array( $this, 'shortcode_amazon_bounties' ) );
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
			$affiliates = $this->widget_affiliates( 'thin' );
		} else {
			$affiliates = $this->widget_affiliates( 'wide' );
		}

		$advert = '<!-- BEGIN Affiliate Ads --><div class="affiliate-ads"><center>' . $affiliates . '</center></div><!-- END Affiliate Ads -->';

		return $advert;
	}

	/**
	 * Static Affiliates
	 *
	 * This includes links to Yikes!, DreamHost, HTLPodcast, and things that stay pretty static.
	 *
	 * @access public
	 * @return array
	 */
	public static function widget_affiliates( $format ) {

		$id     = get_the_ID();
		$local  = array(
			'facetwp'   => '<a href="https://facetwp.com/?ref=91&campaign=LezPress"><img src="' . plugins_url( 'images/facetwp-300x250.png', __FILE__ ) . '"></a>',
			'dreamhost' => '<a href="https://dreamhost.com/dreampress/"><img src="' . plugins_url( 'images/dreamhost-300x250.png', __FILE__ ) . '"></a>',
			'yikes'     => '<a href="https://www.yikesinc.com"><img src="' . plugins_url( 'images/yikes-300x250.png', __FILE__ ) . '"></a>',
		);
		$number = wp_rand();

		if ( 0 === $number % 2 ) {
			$advert = self::cbs( $id, 'widget', $format );
		} elseif ( 0 === $number % 3 ) {
			$advert = self::amazon( $id, 'widget', $format );
		} else {
			if ( 'wide' === $format ) {
				$advert = $local[ array_rand( $local ) ];
			} else {
				$advert = self::cbs( $id, 'widget', $format );
			}
		}

		return $advert;
	}

	/*
	 * Display Amazon Bounties
	 *
	 * THIS IS DEPRECATED!
	 *
	 * @since 1.0
	*/
	public static function shortcode_amazon_bounties( $atts ) {
		$ads = '<!-- Deprecated -->';
		return $ads;
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
	public static function actors( $id, $type ) {
		$return = self::random( $id, $type );
		return $return;
	}

	/**
	 * Determine what to call for characters
	 * This is just random
	 */
	public static function characters( $id, $type ) {
		$return = self::random( $id, $type );
		return $return;
	}

	/**
	 * Determine what to call for shows
	 * This is much more complex!
	 */
	public static function shows( $id, $type ) {

		// Default ads are Amazon
		$return = self::amazon( $id, $type );

		// Show a different show ad depending on things...
		if ( 'affiliate' === $type ) {
			$return = self::affiliate_link( $id );
		} else {
			// Figure out if this is a CBS show
			$on_cbs = self::is_show_cbs( $id );
			if ( $on_cbs ) {
				$return = self::cbs( $id, $type );
			}
		}
		return $return;
	}

	/**
	 * Check if the show is a CBS show...
	 *
	 * @return true/false
	 */
	public static function is_show_cbs( $post_id ) {
		$on_cbs = false;

		$slug         = get_post_field( 'post_name', $post_id );
		$stations     = get_the_terms( $post_id, 'lez_stations' );
		$cbs_stations = array( 'cbs', 'cbs-all-access', 'cw', 'the-cw', 'cw-seed', 'upn', 'wb' );

		// Check if it's a CBS station
		if ( $stations && ! is_wp_error( $stations ) ) {
			foreach ( $stations as $station ) {
				if ( in_array( $station->slug, $cbs_stations, true ) ) {
					$on_cbs = true;
				}
			}
		}

		// Check if it's bloody Star Trek
		if ( strpos( $slug, 'star-trek' ) !== false ) {
			$on_cbs = true;
		}

		return $on_cbs;
	}

	/**
	 * Call something random...
	 * This is a basic check of a random number
	 */
	public static function random( $id, $type ) {
		$number = wp_rand();
		if ( 0 === $number % 2 ) {
			$return = self::cbs( $id, $type );
		} else {
			$return = self::amazon( $id, $type );
		}
		return $return;
	}

	/**
	 * Call Amazon Affilate Data
	 */
	public static function amazon( $id, $type, $format = 'wide' ) {
		require_once 'amazon.php';
		return LWTV_Affiliate_Amazon::show_ads( $id, $type, $format );
	}

	/**
	 * Call CBS Affilate Data
	 */
	public static function cbs( $id, $type, $format = 'wide' ) {
		require_once 'cbs.php';
		return LWTV_Affiliate_CBS::show_ads( $id, $type, $format );
	}

	/**
	 * Call Apple Affiliate Data
	 */
	public static function apple( $id, $type, $format = 'wide' ) {
		require_once 'apple.php';
		return LWTV_Affiliate_Apple::show_ads( $id, $type, $format );
	}

	/**
	 * Call Custom Affiliate Links
	 * This is used by shows to figure out where people can watch things
	 * There's some juggling for certain sites
	 */
	public static function affiliate_link( $id ) {

		$affiliate_url = get_post_meta( $id, 'lezshows_affiliate', true );

		$links = array();

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
					$url   = $clean_url . '/ref=as_li_tl?ie=UTF8&tag=lezpress-20';
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
					$cbs_id = self::cbs( $id, 'text' );
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

// If we aren't on an admin page, let's do this
if ( ! is_admin() ) {
	new LWTV_Affilliates();
}
