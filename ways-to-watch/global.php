<?php
/**
 * Name: Global Data
 * Description: Adds required meta data and services for affiliates
 *
 * The Ways to Watch code is tied into our affiliate deals with certain networks/streamers.
 * This code ensures the data is loaded on all pages to properly attribute us.
 *
 * Links:
 * https://appleservices-console.partnerize.com/v2/overview/overview
 * https://affiliate.itunes.apple.com/resources/documentation/basic_affiliate_link_guidelines_for_the_phg_network/
 */


class LWTV_Affiliate_Global {

	/**
	 * __construct function.
	 */
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'apple_auto_link_maker' ) );
		add_filter( 'the_content', array( $this, 'amazon_publisher_studio' ), 11 );
		add_action( 'wp_head', array( $this, 'add_meta_tags' ), 2 );
	}

	/**
	 * Add Meta Tags for Affiliates
	 */
	public function add_meta_tags() {
		// https://impact.com
		echo '<meta name="ir-site-verification-token" value="1145177634" />';
	}

	/**
	 * Insert Apple's Auto Link Maker
	 * https://autolinkmaker.itunes.apple.com/?at=1010lMaT
	 */
	public function apple_auto_link_maker() {
		echo "<script type='text/javascript'>var _merchantSettings=_merchantSettings || [];_merchantSettings.push(['AT', '1010lMaT']);(function(){var autolink=document.createElement('script');autolink.type='text/javascript';autolink.async=true; autolink.src= ('https:' == document.location.protocol) ? 'https://autolinkmaker.itunes.apple.com/js/itunes_autolinkmaker.js' : 'http://autolinkmaker.itunes.apple.com/js/itunes_autolinkmaker.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(autolink, s);})();</script>";
	}

	/**
	 * Parse content and edit Amazon URLs to have our tag at the end.
	 *
	 * @param  [string] $text [URL]
	 * @return [string]       [URL with our tag added]
	 */
	public function amazon_publisher_studio( $post_content ) {
		// Default: Content is content, unchanged.
		$content = $post_content;

		// What kind of URL we're looking for:
		$regex_url = '#<a href="(?:https://(?:www\.){0,1}amazon\.com(?:/.*){0,1}(?:/dp/|/gp/product/))(.*?)(?:/.*|$)"#';

		/**
		 * $url is what preg_match() will return.
		 *
		 * $url filled with the results of search. $url[0] will contain the text that matched the full pattern,
		 * $url[1] will have the text that matched the first captured parenthesized subpattern, and so on.
		 *
		 * See: https://www.php.net/manual/en/function.preg-match.php
		 */
		if ( preg_match( $regex_url, $post_content, $url ) ) {
			// If there is a match, we need to update the URL to include our tag.
			$linkurl = rtrim( $url[0], '"' );
			$content = preg_replace( $regex_url, $linkurl . '?tag=lezpress-20"', $post_content );
		}

		return $content;
	}
}
