<?php
/**
 * Name: Affiliate Code for Amazon
 * Description: Auto Ads in Amazon Affiliate Code
 *
 * https://affiliate-program.amazon.com/home/bannerlinks
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
	public function show_ads( $post_id, $format = 'wide' ) {
		$the_ad = self::output_widget( $post_id, $format );
		return $the_ad;
	}

	/**
	 * Output widget
	 */
	public function output_widget( $post_id, $format ) {
		$output = self::bounty( $post_id, $format );
		return $output;
	}

	/**
	 * Generate a random bounty
	 */
	public function bounty( $post_id, $format ) {

		$amazon_ads = array(
			// 728 x 90
			'banner' => array(
				'generic'       => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ez&f=ifr&linkID=1322821e7342c553c9da30c3c97fb530&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'entertainment' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=musicandentertainmentrot&f=ifr&linkID=16f9c1595d1ac2f8c90b8937cea916b1&t=lezpress-20&tracking_id=ipsteorg-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'fire-tv'       => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=amzn_smp_ftve_tpr_20&banner=0MFVZA0J97YPGQNEZCR2&f=ifr&linkID=ec4db54660d85fbf15170f43a3e35637&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'snacks'        => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primemain&banner=1FM3MKXZNJ0PPEBRV2R2&f=ifr&linkID=200e0e3a4a77c5f2d4ba32efc3deb8c3&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'onemembership' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primemain&banner=1GEX47SY8CX59NQN5W02&f=ifr&linkID=01da21198fe3a35f7f139ecf9e4d68f9&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'30-day-trial'  => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primemain&banner=0ZPFACTPTMFJ1D5D3AG2&f=ifr&linkID=189d35f6f06648bffc43e48be9644f40&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'30-day-trial2' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primemain&banner=17T0MQR5WKGJCZNRHCG2&f=ifr&linkID=8c368810243109504887e397ec9f3901&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 300x250
			'wide'   => array(
				'generic'       => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ez&f=ifr&linkID=32f8062b010aa3526e96f8bd288091e1&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'snacks'        => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemain&banner=1A1582R37K6AZB4ZETG2&f=ifr&linkID=4dadd304433b96e2b275df9da6215b0a&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'onemembership' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemain&banner=1Q4PWNSXQKNZYTCZ1RR2&f=ifr&linkID=cf220b741be2f9be488471abd7632cd6&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'30-day-trial'  => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemain&banner=1WK0VRNA75J95DFZRE02&f=ifr&linkID=309b4955918ce7c11ede5a3e75ee8ad9&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'30-day-trial2' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemain&banner=028WNSXDMC6H5YDNCB82&f=ifr&linkID=8e54b6f3e4cbafd36178f9544cc508f2&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 160x600
			'thin'   => array(
				'generic'       => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ez&f=ifr&linkID=025586ca828ec6a2416539d52876b706&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'30-day-trial'  => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primemain&banner=0GCYTHFZDJTVVMVYTQR2&f=ifr&linkID=d3d3e79c7a54f18c0b6a92146c181ed6&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'30-day-trial2' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primemain&banner=161E7D8SBJFWK4M9MNG2&f=ifr&linkID=dd3b7f082d5b853ae226330f868c8698&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 468x60
			'tiny'   => array(
				'generic'       => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=13&l=ez&f=ifr&linkID=74a20b4c2fd2a645e590128a42bb0fd1&t=lezpress-20&tracking_id=lezpress-20" width="468" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'30-day-trial'  => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=13&l=ur1&category=primemain&banner=0NVA2PKRAY588CJNAYG2&f=ifr&linkID=8dfbb0bf52e136a374e605801106bc4f&t=lezpress-20&tracking_id=lezpress-20" width="468" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'30-day-trial2' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=13&l=ur1&category=primemain&banner=0KGQBGGDPF1BVBFX4BG2&f=ifr&linkID=6c1c0b03fc5d315b8eeeffe343c76992&t=lezpress-20&tracking_id=lezpress-20" width="468" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			'text'   => array(
				'join' => '<a target="_blank" href="https://www.amazon.com/b/?rh=i:instant-video,n:2858778011&ie=UTF8&filterId=OFFER_FILTER=SUBSCRIPTIONS&node=2858778011&ref_=assoc_tag_ph_1465430649312&_encoding=UTF8&camp=1789&creative=9325&linkCode=pf4&tag=lezpress-20&linkId=db98f9805eb2ff043fd508b10b63ab85">Join Prime Video Channels Free Trial</a><img src="//ir-na.amazon-adsystem.com/e/ir?t=lezpress-20&l=pf4&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />',
			),
		);

		$the_ad = $amazon_ads[ $format ][ array_rand( $amazon_ads[ $format ] ) ];

		return $the_ad;
	}

}

new LWTV_Affiliate_Amazon();
