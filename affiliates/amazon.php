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
				'generic'         => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ez&f=ifr&linkID=8bb8d05a4776d19998555a01315b0b46&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'gift-card'       => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=gift_certificates&banner=1G274HKHXM7QERC7YAG2&f=ifr&linkID=abf61403773717b927d61a4621425e8e&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-discount'  => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primediscounted&banner=0E2AV3Z1P3Q26AS9T8R2&f=ifr&linkID=505e4e1b54565964398d7f0212b53d9a&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-free-trial' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primemain&banner=1FM3MKXZNJ0PPEBRV2R2&f=ifr&linkID=9e23f20458424a91fd29b42f3cced7b3&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-gift'      => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primegift&banner=0DHKFFV9MZ6GTWBFZK82&f=ifr&linkID=d7fa444b6099b6a9be5fc24e62735dc0&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'fire-tv'         => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=amzn_smp_tk_ld&banner=1Y2A4TXM7848HZKXA802&f=ifr&linkID=30af62d9c19b67f77c73360838f74849&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 300x250
			'wide'   => array(
				'generic'        => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ez&f=ifr&linkID=045ba88c2c0ea2a3ac346143065df964&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-discount' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primediscounted&banner=0B2TM48Z6X1RA9B3TZR2&f=ifr&lc=pf4&linkID=2ed7635a135e3ea6dab5e1c6dd6af650&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'gift-card'      => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=gift_certificates&banner=127JF9E4530CSFRCY4R2&f=ifr&linkID=4a27a7fa1aab2142e278437be891f4d4&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-discount' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primediscounted&banner=0B2TM48Z6X1RA9B3TZR2&f=ifr&linkID=7dacd32f51bef59378e19fcb1be19cd1&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-gift'     => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primegift&banner=10BSGCHPCJG0HZN45H82&f=ifr&lc=pf4&linkID=95a056ce126ce6fec07fc3749b907f57&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-trial'    => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemain&banner=028WNSXDMC6H5YDNCB82&f=ifr&lc=pf4&linkID=02a45d2bfd5cceb44324b1f219b3cd2c&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-trial-2'  => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemain&banner=1A1582R37K6AZB4ZETG2&f=ifr&linkID=46eafebea7cf4f9538f6585ea84aa206&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-gift'     => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primegift&banner=10BSGCHPCJG0HZN45H82&f=ifr&linkID=0cb97c8a6552779f63edcb26a32f75ea&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'fire-tv'        => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=amzn_smp_tk_ld&banner=01RDX83ZGPJ45HQST802&f=ifr&linkID=629a14831f66d2bd114004c029feb7cc&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',

			),
			// 160x600
			'thin'   => array(
				'generic'        => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ez&f=ifr&linkID=46df5e4b638925b499a5865e6f7d3c98&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'gift-card'      => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=gift_certificates&banner=0S32YAVKXXKQGNQSSGG2&f=ifr&linkID=1ba3a5850f5c837dbb6692eecdd38f57&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-discount' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primediscounted&banner=1GHG3BRZ09TEH5G5A4R2&f=ifr&linkID=61d1b6fcd8d6e362c268c0dabaeee8a2&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-trial'    => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primemain&banner=0GCYTHFZDJTVVMVYTQR2&f=ifr&linkID=75a7c9e07e881018c67b35a547ad794b&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-gift'     => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primegift&banner=0ZWKW7ZFNM91W64BCX02&f=ifr&linkID=e4bdcd535bc7ea0981bb36bb3925603d&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'fire-tv'        => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=amzn_smp_tk_ld&banner=1B4BCGN3YP1JDS7W2C82&f=ifr&linkID=e2ed8df6298c2c683e3b5b060541cb86&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',

			),
			// 468x60
			'tiny'   => array(
				'generic'        => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=7&l=ez&f=ifr&linkID=1dc4c86661c2f171d6741e6990e3dd51&t=lezpress-20&tracking_id=lezpress-20" width="468" height="40" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'gift-card'      => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=40&l=ur1&category=gift_certificates&banner=17C42ZZAJ4Q6Z4CM3WR2&f=ifr&linkID=d12d7635d594e2ee488ee484864454e0&t=lezpress-20&tracking_id=lezpress-20" width="120" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-discount' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=20&l=ur1&category=primediscounted&banner=0305MWZXSKZPPS8VVXR2&f=ifr&linkID=f2e563780775a0b46dd23029540740ce&t=lezpress-20&tracking_id=lezpress-20" width="120" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'prime-trial'    => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=13&l=ur1&category=primemain&banner=0NVA2PKRAY588CJNAYG2&f=ifr&linkID=ec6b61f68ded3e734b3d0c8e1f88f2b2&t=lezpress-20&tracking_id=lezpress-20" width="468" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'fire-tv'        => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=288&l=ur1&category=amzn_smp_tk_ld&banner=0DW04N1JF3G15355TM82&f=ifr&linkID=740d816b27c5b4f21440f304741e2c01&t=lezpress-20&tracking_id=lezpress-20" width="320" height="50" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
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
