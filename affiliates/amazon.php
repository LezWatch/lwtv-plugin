<?php
/**
 * Name: Affiliate Code for Amazon
 * Description: Auto Ads in Amazon Affiliate Code
 *
 * https://affiliate-program.amazon.com/home/bannerlinks
 * https://affiliate-program.amazon.com/home/bounties/all?category=amazontv
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
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=musicandentertainmentrot&f=ifr&linkID=1fddd2f55c9945c95e51680a7b128426&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primemain&banner=1FM3MKXZNJ0PPEBRV2R2&f=ifr&linkID=276e2c8c65183edb2648c358e5f2a8de&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primemain&banner=1GEX47SY8CX59NQN5W02&f=ifr&linkID=fee9fa0a47a8e678452324f52700cd87&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primemain&banner=0ZPFACTPTMFJ1D5D3AG2&f=ifr&linkID=b22afd99d631062d14a3ca21be26357e&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primemain&banner=17T0MQR5WKGJCZNRHCG2&f=ifr&linkID=e9896eeb0396045b7db5459a7c07c602&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=prime_up&banner=1FTSYTFQFDNHN73ZB4G2&f=ifr&linkID=8ab15c6ed35cd9994974cabe352a19e2&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=prime_up&banner=0YFQ25QVCHBS31VNWX82&f=ifr&linkID=633f0fc3dae8c5c16e0ce0a09ebfa392&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 300x250
			'wide'   => array(
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=musicandentertainmentrot&f=ifr&linkID=b91505220d5f8994d68fc2b970a6d8f7&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemain&banner=1A1582R37K6AZB4ZETG2&f=ifr&linkID=4a65d74ae0673a21e3b1bee5cc963cea&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemain&banner=1Q4PWNSXQKNZYTCZ1RR2&f=ifr&linkID=28a533327e8ed6d4ad888153e62b36e0&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemain&banner=1WK0VRNA75J95DFZRE02&f=ifr&linkID=f714de92c1f08f041eeb9be12c99d8e3&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemain&banner=028WNSXDMC6H5YDNCB82&f=ifr&linkID=b7ecbbcfbe5b878efd5c85c2f37524ef&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=prime_up&banner=1S5P58JDRT3PRAFKVR82&f=ifr&linkID=8cfd1b51f254e479b22e0772c7cc2acd&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=prime_up&banner=1305TN329QGG25KXRA82&f=ifr&linkID=28e3cadee26a5fa7f9e00487f344491a&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 160x600
			'thin'   => array(
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primemain&banner=0GCYTHFZDJTVVMVYTQR2&f=ifr&linkID=71ee164719540a217eccef549c792675&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primemain&banner=161E7D8SBJFWK4M9MNG2&f=ifr&linkID=d7e1aa9152f5d0aaf66e58f8348cf50e&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=prime_up&banner=19ND584SQ3QE4J510282&f=ifr&linkID=937c99e779de43782ffd235e472cdd14&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=prime_up&banner=1HM90HKK2Z9E1WWA2382&f=ifr&linkID=2a8acb3c33b979135f12938283037922&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 468x60
			'tiny'   => array(
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=13&l=ur1&category=primemain&banner=0NVA2PKRAY588CJNAYG2&f=ifr&linkID=4ff6edbcc4071be31a6ebf8122ed9aae&t=lezpress-20&tracking_id=lezpress-20" width="468" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=13&l=ur1&category=primemain&banner=0KGQBGGDPF1BVBFX4BG2&f=ifr&linkID=e7aed31e99eee225b62857ff225b528c&t=lezpress-20&tracking_id=lezpress-20" width="468" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=26&l=ur1&category=prime_up&banner=1D1Z1K1TYN40YBSP5VG2&f=ifr&linkID=8bfe54960b88b9ae934e84802d985656&t=lezpress-20&tracking_id=lezpress-20" width="468" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=26&l=ur1&category=prime_up&banner=0DA7GS9QPQZK47EK28R2&f=ifr&linkID=c51b7fb19e5883b6d4519213dd64960a&t=lezpress-20&tracking_id=lezpress-20" width="468" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			'text'   => array(
				'<a target="_blank" href="https://www.amazon.com/b/?rh=i:instant-video,n:2858778011&ie=UTF8&filterId=OFFER_FILTER=SUBSCRIPTIONS&node=2858778011&ref_=assoc_tag_ph_1465430649312&_encoding=UTF8&camp=1789&creative=9325&linkCode=pf4&tag=lezpress-20&linkId=dd7f201a8e25c7abdcc472c32008fcc2">Join Prime Video Channels Free Trial</a><img src="//ir-na.amazon-adsystem.com/e/ir?t=lezpress-20&l=pf4&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />',
			),
		);

		$the_ad = $amazon_ads[ $format ][ array_rand( $amazon_ads[ $format ] ) ];
		return $the_ad;
	}

}

new LWTV_Affiliate_Amazon();
