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

		$thisad = array_rand( $affiliates );

		$advert = '<!-- BEGIN Affiliate Ads --><div class="affiliate-ads ' . sanitize_html_class( $thisad ) . '"><center>' . $affiliates[ $thisad ] . '</center></div><!-- END Affiliate Ads -->';

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
	public static function widget_affiliates( $type ) {

		$affiliates = array(
			'wide' => array(
				'facetwp'    => '<a href="https://facetwp.com/?ref=91&campaign=LezPress"><img src="' . plugins_url( 'images/facetwp-300x250.png', __FILE__ ) . '"></a>',
				'dreamhost'  => '<a href="https://dreamhost.com/dreampress/"><img src="' . plugins_url( 'images/dreamhost-300x250.png', __FILE__ ) . '"></a>',
				'yikes'      => '<a href="https://www.yikesinc.com"><img src="' . plugins_url( 'images/yikes-300x250.png', __FILE__ ) . '"></a>',
				'htlpodcast' => '<iframe src="//banners.itunes.apple.com/banner.html?partnerId=&aId=1010lMaT&bt=catalog&t=catalog_white&id=1254294886&c=us&l=en-US&w=300&h=250&store=podcast" frameborder=0 style="overflow-x:hidden;overflow-y:hidden;width:300px;height:250px;border:0px"></iframe>',
				'apple'      => '<iframe src="https://widgets.itunes.apple.com/widget.html?c=us&brc=FFFFFF&blc=FFFFFF&trc=FFFFFF&tlc=FFFFFF&d=&t=&m=tvSeason&e=tvSeason&w=250&h=300&ids=&wt=search&partnerId=&affiliate_id=&at=1010lMaT&ct=" frameborder=0 style="overflow-x:hidden;overflow-y:hidden;width:250px;height: 300px;border:0px"></iframe>',
				'cbs-goodf'  => '<iframe src="//a.impactradius-go.com/gen-ad-code/1242493/456232/3065/" width="300" height="600" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>',
				'cbs-disco'  => '<iframe src="//a.impactradius-go.com/gen-ad-code/1242493/447373/3065/" width="300" height="600" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>',
				'cbs-madms'  => '<iframe src="//a.impactradius-go.com/gen-ad-code/1242493/379711/3065/" width="300" height="600" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>',
				'amazon'     => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=49&l=ur1&category=primeent&banner=1NBTV8WAYWJ1X9PVW582&f=ifr&linkID=e1b38a7992eaf253a93dcf650eac5ca5&t=lezpress-20&tracking_id=lezpress-20" width="300" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			'thin' => array(
				'amazon1' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primeent&banner=0XFKWQVGDFG5VJ2ARBG2&f=ifr&linkID=736cbb4746cfdde557e02035fbef63d5&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'amazon2' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=29&l=ur1&category=primeent&banner=1QQ0YMZ637C55CZGYWG2&f=ifr&linkID=9acd7e889a1fad94c7bd669757ba1d65&t=lezpress-20&tracking_id=lezpress-20" width="120" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'amazon3' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=29&l=ur1&category=primeent&banner=15P2GYM6FRRM04V3BH82&f=ifr&linkID=26d0f2ae73cd170858d9a5be39dd6c9e&t=lezpress-20&tracking_id=lezpress-20" width="120" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'amazon4' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=29&l=ur1&category=primeent&banner=18FYEX4M5XZVHBA6HF82&f=ifr&linkID=0f4f7a8109060b84c928d19e4f649855&t=lezpress-20&tracking_id=lezpress-20" width="120" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'amazon5' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=29&l=ur1&category=primeent&banner=09PV71N3XGSGHA4MER02&f=ifr&linkID=d459953344c1e054d052faafedb9289f&t=lezpress-20&tracking_id=lezpress-20" width="120" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'cbs1'    => '<iframe src="//a.impactradius-go.com/gen-ad-code/1242493/359934/3065/" width="160" height="600" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>',
				'cbs2'    => '<iframe src="//a.impactradius-go.com/gen-ad-code/1242493/359964/3065/" width="160" height="600" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>',
				'cbs3'    => '<iframe src="//a.impactradius-go.com/gen-ad-code/1242493/359962/3065/" width="160" height="600" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>',
				'cbs4'    => '<iframe src="//a.impactradius-go.com/gen-ad-code/1242493/455991/3065/" width="160" height="600" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>',
				'cbs5'    => '<iframe src="//a.impactradius-go.com/gen-ad-code/1242493/379709/3065/" width="160" height="600" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>',
			),
		);

		return $affiliates[ $type ];
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
	 */
	public static function actors( $id, $type ) {
		// Default: Random
		$return = self::random( $id, $type );
		return $return;
	}

	/**
	 * Determine what to call for characters
	 */
	public static function characters( $id, $type ) {
		// Default: Random
		$return = self::random( $id, $type );
		return $return;
	}

	/**
	 * Determine what to call for shows
	 * This is much more complex!
	 */
	public static function shows( $id, $type ) {

		// Default: Amazon if the transient expired, else Apple.
		$amazon_transient = get_transient( 'lezwatchtv_amazon_affiliates' );
		if ( false === $amazon_transient ) {
			$return = self::amazon( $id, $type );
		} else {
			$return = self::apple( $id, $type );
		}

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
		if ( 0 === $number % 3 ) {
			$return = self::apple( $id, $type );
		} elseif ( 0 === $number % 2 ) {
			$return = self::cbs( $id, $type );
		} else {
			$return = self::amazon( $id, $type );
		}
		return $return;
	}

	/**
	 * Call Amazon Affilate Data
	 */
	public static function amazon( $id, $type ) {
		require_once 'amazon.php';
		return LWTV_Affiliate_Amazon::show_ads( $id, $type );
	}

	/**
	 * Call CBS Affilate Data
	 */
	public static function cbs( $id, $type ) {
		require_once 'cbs.php';
		return LWTV_Affiliate_CBS::show_ads( $id, $type );
	}

	/**
	 * Call Apple Affiliate Data
	 */
	public static function apple( $id, $type ) {
		require_once 'apple.php';
		return LWTV_Affiliate_Apple::show_ads( $id, $type );
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
					$url     = $clean_url . '/ref=as_li_tl?ie=UTF8&tag=lezpress-20';
					$links[] = '<a href="' . $url . '" target="_blank" class="btn btn-primary">Amazon Prime</a><img src="//ir-na.amazon-adsystem.com/e/ir?t=lezpress-20&l=pf4&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />';
					break;
				case 'apple':
				case 'itunes':
					$url     = $clean_url . '?mt=4&at=1010lMaT';
					$links[] = '<a href="' . $url . '" target="_blank" class="btn btn-primary">iTunes</a>';
					break;
				case '7eer':
				case 'cbs':
					$links[] = self::cbs( $id, 'text' );
					break;
				case 'abc':
				case 'nbc':
					$links[] = '<a href="' . $url . '" target="_blank" class="btn btn-primary">' . strtoupper( $hostname ) . '</a>';
					break;
				case 'bbcamerica':
					$links[] = '<a href="' . $url . '" target="_blank" class="btn btn-primary">BBC America</a>';
					break;
				case 'cwtv':
					$links[] = '<a href="' . $url . '" target="_blank" class="btn btn-primary">The CW</a>';
					break;
				case 'youtube':
					$links[] = '<a href="' . $url . '" target="_blank" class="btn btn-primary">YouTube</a>';
					break;
				case 'tellofilms':
					$links[] = '<a href="' . $url . '" target="_blank" class="btn btn-primary">Tello</a>';
					break;
				case 'showtimeanytime':
					$links[] = '<a href="' . $url . '" target="_blank" class="btn btn-primary">Showtime</a>';
					break;
				default:
					$links[] = '<a href="' . $url . '" target="_blank" class="btn btn-primary">' . ucfirst( $hostname ) . '</a>';
					break;
			}
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
