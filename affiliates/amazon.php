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
				'mrs_maisel'      => array(
					'banner'   => '01R2KB5YZTMAPA6S78G2',
					'linkid'   => 'cd58c3115c653817eb87591873118c47',
					'category' => 'pivcreative',
				),
				'mrs_maisel2'     => array(
					'banner'   => '1MMY8RN9HZQGJ3BQYWR2',
					'linkid'   => 'a3b11d68346f2cd5ace5f21f95f03782',
					'category' => 'pivcreative',
				),
				'pride'           => array(
					'banner'   => '1PPVF74KGV3PT8CB9JR2',
					'linkid'   => '7d1eb87514ea152591c9d7c044117f78',
					'category' => 'primeent',
				),
				'generic'         => array(
					'banner'   => '031XMRKZ05JH88GSKT82',
					'linkid'   => '9d58c4569065088e9524731135eff1de',
					'category' => 'primeent',
				),
				'free_trial'      => array(
					'banner'   => '028WNSXDMC6H5YDNCB82',
					'linkid'   => 'e6e3908dd31c51c4e18bcfca54d1e587',
					'category' => 'primemain',
				),
				'game_of_thrones' => array(
					'banner'   => '0CT4HNQD8KYJQS5WFKR2',
					'linkid'   => '23fca2d607d068cd43860dd5e396dae2',
					'category' => 'primevideochannels',
				),
				'showtime'        => array(
					'banner'   => '1TAG79C3PQ0GFXZ39R82',
					'linkid'   => '8cd0644bdc327150448285bb60413608',
					'category' => 'primevideochannels',
				),
				'cbs_all_access'  => array(
					'banner'   => '15WAHRRCWADG8X4F09G2',
					'linkid'   => 'bd76da295acbe38c90c994639e093555',
					'category' => 'primevideochannels',
				),
				'starz'           => array(
					'banner'   => '0GJR9JYSTS77TVQ2EH82',
					'linkid'   => 'b85cadafad6e2f6f9b4b4862703ca3c2',
					'category' => 'primevideochannels',
				),
				'hbo'             => array(
					'banner'   => '1E0AR7ZBTK5HEDE0CM82',
					'linkid'   => '4d909c2f7f25c8221f70819dbf2b15fe',
					'category' => 'primevideochannels',
				),
				'westworld'       => array(
					'banner'   => '150VYK260KXYESVKAA02',
					'linkid'   => '3eb555d0bf6aab543496ee8f8559dea6',
					'category' => 'primevideochannels',
				),
				'britbox'         => array(
					'banner'   => '06V9DZJBZ21E92635K82',
					'linkid'   => 'a648bf9d560123577df92189b27666d2',
					'category' => 'primevideochannels',
				),
			),
			// 160x600
			'thin' => array(
				'mrs_maisel'      => array(
					'banner'   => '179M3M4HZVSRN11NY602',
					'linkid'   => '98ead27a6588ed4a47e5c73127cf3934',
					'category' => 'primeent',
				),
				'free_trial'      => array(
					'banner'   => '161E7D8SBJFWK4M9MNG2',
					'linkid'   => 'e44b5b49165dffff0775bdcc19b8837e',
					'category' => 'primemain',
				),
				'game_of_thrones' => array(
					'banner'   => '1KRDJGQFJG64G8WX9VG2',
					'linkid'   => '13a641a9725b9152ebfabbe71a63d600',
					'category' => 'primevideochannels',
				),
				'showtime'        => array(
					'banner'   => '0ETV0M1DQEFGJ83G8M02',
					'linkid'   => '3d40187e7e6e92edda113937f7f0351f',
					'category' => 'primevideochannels',
				),
				'cbs_all_access'  => array(
					'banner'   => '1RSA96WAH3YGF1HZHCG2',
					'linkid'   => '03f4dc485374936e354fa2e0a0631211',
					'category' => 'primevideochannels',
				),
				'hbo'             => array(
					'banner'   => '0TZTZKMZNERPRPZYXN82',
					'linkid'   => '708dd271d014cf55bc7896f3c869f4ce',
					'category' => 'primevideochannels',
				),
				'britbox'         => array(
					'banner'   => '062FRX60FAE1CTM89V82',
					'linkid'   => '1706413e37b21dd457a799ddd6c21cfa',
					'category' => 'primevideochannels',
				),
				'westworld'       => array(
					'banner'   => '068G1JD09XF3D3H7QXG2',
					'linkid'   => 'b98208f39f5f81f57d6d42710877c5b8',
					'category' => 'primevideochannels',
				),
			),
		);

		switch ( $format ) {
			case 'banner':
				$the_ad = '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primeent&banner=06GDZC4PE16KAQ62F782&f=ifr&linkID=b986e9f8e6e94e95b3f8dd33b97833e8&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';
				break;
			case 'text':
				$the_ad = '<a target="_blank" href="https://www.amazon.com/b/?rh=i:instant-video,n:2858778011&ie=UTF8&filterId=OFFER_FILTER=SUBSCRIPTIONS&node=2858778011&ref_=assoc_tag_ph_1465430649312&_encoding=UTF8&camp=1789&creative=9325&linkCode=pf4&tag=lezpress-20&linkId=db98f9805eb2ff043fd508b10b63ab85">Join Prime Video Channels Free Trial</a><img src="//ir-na.amazon-adsystem.com/e/ir?t=lezpress-20&l=pf4&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />';
				break;
			case 'tiny':
				$the_ad = '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=288&l=ur1&category=primeent&banner=1JYFV583HV833YKS3M82&f=ifr&linkID=18ec8f2348142042016ab12347871d9c&t=lezpress-20&tracking_id=lezpress-20" width="320" height="50" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';
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
