<?php
/**
 * Name: Affiliate Code for Amazon
 * Description: Auto Ads in Amazon Affiliate Code
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Amazon
 */

class LWTV_Affiliate_Amazon {

	/**
	 * Determine what ad to show
	 */
	public static function show_ads( $post_id, $format = 'wide' ) {
		$the_ad = self::output_widget( $post_id, $format );
		return $the_ad;
	}

	/**
	 * Output widget
	 */
	public static function output_widget( $post_id, $format ) {
		$output = self::bounty( $post_id, $format );
		return $output;
	}

	/**
	 * Generate a random bounty
	 */
	public static function bounty( $post_id, $format ) {

		$amazon_ads = array(
			'wide' => array(
				// 300x250
				'mrs_maisel'    => array(
					'banner'   => '01R2KB5YZTMAPA6S78G2',
					'linkid'   => '0bbbdb2fc387b2ea73aa892e13eba9dd',
					'category' => 'pivcreative',
				),
				'pride'         => array(
					'banner'   => '1PPVF74KGV3PT8CB9JR2',
					'linkid'   => 'c696d4f5c174afd46fb8f5bdee9ed9b5',
					'category' => 'primeent',
				),
				'westworld'     => array(
					'banner'   => '150VYK260KXYESVKAA02',
					'linkid'   => 'fc4eb97adbbe0e281984a205d79b559a',
					'category' => 'primevideochannels',
				),
				'britbox'       => array(
					'banner'   => '06V9DZJBZ21E92635K82',
					'linkid'   => '9d76bd102350dec1360349cd20089b71',
					'category' => 'primevideochannels',
				),
				'gameofthrones' => array(
					'banner'   => '14CSX9S0TYDSWZT15502',
					'linkid'   => '94949318c7e79a2fb42cbd909a8318a0',
					'category' => 'primevideochannels',
				),
			),
			// 160x600
			'thin' => array(
				'mrs_maisel'    => array(
					'banner'   => '179M3M4HZVSRN11NY602',
					'linkid'   => 'f3426cf628b26e75a43112ee3c53099d',
					'category' => 'primeent',
				),
				'free_trial'    => array(
					'banner'   => '161E7D8SBJFWK4M9MNG2',
					'linkid'   => '20b14469a69f73ba970a4a56ae91f24a',
					'category' => 'primemain',
				),
				'showtime'      => array(
					'banner'   => '1KDYJTM5G5NPVXW1V2R2',
					'linkid'   => '93c0604172b79cce3e8341d959d80b78',
					'category' => 'primevideochannels',
				),
				'starz'         => array(
					'banner'   => '0N482BNAZMAW3F4223G2',
					'linkid'   => '5d0088e5d0477d7368f2aac6a45d1eef',
					'category' => 'primevideochannels',
				),
				'cbs'           => array(
					'banner'   => '1RSA96WAH3YGF1HZHCG2',
					'linkid'   => 'b2a1cc5466874a57f58b9d36baab663c',
					'category' => 'primevideochannels',
				),
				'westworld'     => array(
					'banner'   => '068G1JD09XF3D3H7QXG2',
					'linkid'   => '8a9058184c21a6625e8c396eaff6dc1d',
					'category' => 'primevideochannels',
				),
				'britbox'       => array(
					'banner'   => '062FRX60FAE1CTM89V82',
					'linkid'   => '26db635a13dc4f3ffef10ed3dbfb6ad4',
					'category' => 'primevideochannels',
				),
				'gameofthrones' => array(
					'banner'   => '06BB0SBR8G81YR8KW9R2',
					'linkid'   => '3719c3f34df27cd6ac5e64b4ab55ed4d',
					'category' => 'primevideochannels',
				),
			),
		);

		switch ( $format ) {
			case 'banner':
				$the_ad = '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primeent&banner=1JDKYZ80A7KX1C3HKRG2&f=ifr&linkID=ac78fdea1927d668e2619ecf07d8fcba&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';
				break;
			case 'text':
				$the_ad = '<a target="_blank" href="https://www.amazon.com/b/?rh=i:instant-video,n:2858778011&ie=UTF8&filterId=OFFER_FILTER=SUBSCRIPTIONS&node=2858778011&ref_=assoc_tag_ph_1465430649312&_encoding=UTF8&camp=1789&creative=9325&linkCode=pf4&tag=lezpress-20&linkId=db98f9805eb2ff043fd508b10b63ab85">Join Prime Video Channels Free Trial</a><img src="//ir-na.amazon-adsystem.com/e/ir?t=lezpress-20&l=pf4&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />';
				break;
			case 'tiny':
				$the_ad = '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=40&l=ur1&category=gift_certificates&banner=17C42ZZAJ4Q6Z4CM3WR2&f=ifr&linkID=498d07f9b64711b033c7381235278268&t=lezpress-20&tracking_id=lezpress-20" width="120" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';
				break;
			default:
				// A random ad will be served
				$pick_ad = $amazon_ads[ $format ][ array_rand( $amazon_ads[ $format ] ) ];
				$sizes   = explode( 'x', LWTV_Affilliates::$format_sizes[ $format ] );
				$size    = 'width="' . $sizes[0] . '" height="' . $sizes[1] . '"';
				$the_ad  = '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=' . $pick_ad['category'] . '&banner=' . $pick_ad['banner'] . '&f=ifr&linkID=' . $pick_ad['linkid'] . '&t=lezpress-20&tracking_id=lezpress-20" ' . $size . ' scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';
		}

		return $the_ad;
	}

}

new LWTV_Affiliate_Amazon();
