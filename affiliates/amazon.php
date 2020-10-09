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
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=musicandentertainmentrot&f=ifr&linkID=edb4a92539405171d15abbc8bf040a82&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ez&f=ifr&linkID=6b7d7fb8f0621757fb908dc1ff5214d1&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=gift_certificates&banner=1G274HKHXM7QERC7YAG2&f=ifr&linkID=c7a39615d03ed1453e7b19610ddf1e31&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=amzn_smp_snp_scar&banner=1CZ54CSQ0ZE8XEN39QR2&f=ifr&linkID=55562918983e6df4b5db400466887107&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primemain&banner=1FM3MKXZNJ0PPEBRV2R2&f=ifr&linkID=ce80568f5db2368cdf0412efcc2e1129&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 300x250
			'wide'   => array(
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=musicandentertainmentrot&f=ifr&linkID=0e72534fc603bdc84cdad5d0e34b5e83&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ez&f=ifr&linkID=0d2d51ce0a0951c46f24c2ca53d6f62f&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=gift_certificates&banner=127JF9E4530CSFRCY4R2&f=ifr&linkID=24a5f03445e1b1d6a328b48a5a5e15b6&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=amzn_smp_snp_scar&banner=0ZR2EC3G88GXAA4MA502&f=ifr&linkID=aa071cbbf844de0aefac9ec811338bd1&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 160x600
			'thin'   => array(
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=musicandentertainmentrot&f=ifr&linkID=9013377a8197afd3254db12980c03017&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ez&f=ifr&linkID=359d6c6fea21624f44ab75a14b3d0a02&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=gift_certificates&banner=0S32YAVKXXKQGNQSSGG2&f=ifr&linkID=9068cba19c9025166034a4cfaf208067&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=amzn_smp_snp_scar&banner=1FV2JZJZZM7XGHJEZ882&f=ifr&linkID=0e3317dd4e18be78cff765f4e71d9630&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 468x60
			'tiny'   => array(
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=13&l=ez&f=ifr&linkID=6bd29f3f269236f9d587b6609f340bef&t=lezpress-20&tracking_id=lezpress-20" width="468" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=40&l=ur1&category=gift_certificates&banner=17C42ZZAJ4Q6Z4CM3WR2&f=ifr&linkID=37136cad51020335ac63d949b3b5cd16&t=lezpress-20&tracking_id=lezpress-20" width="120" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=13&l=ur1&category=gift_certificates&banner=180TQ0K9X17QCCZQS4R2&f=ifr&linkID=d7779a693415dac68767c274be3c4700&t=lezpress-20&tracking_id=lezpress-20" width="468" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=288&l=ur1&category=gift_certificates&banner=1ZMTYQQCPDNJYWMYWR82&f=ifr&linkID=19dfb21181f16305aa6f5ab83ab75ec4&t=lezpress-20&tracking_id=lezpress-20" width="320" height="50" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=288&l=ur1&category=amzn_smp_snp_scar&banner=0KQWTF15VA4K12QFCJR2&f=ifr&linkID=703a41517a4d054907519c47510927d7&t=lezpress-20&tracking_id=lezpress-20" width="320" height="50" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			'text'   => array(
				'<a target="_blank" href="https://www.amazon.com/b/?rh=i:instant-video,n:2858778011&ie=UTF8&filterId=OFFER_FILTER=SUBSCRIPTIONS&node=2858778011&ref_=assoc_tag_ph_1465430649312&_encoding=UTF8&camp=1789&creative=9325&linkCode=pf4&tag=lezpress-20&linkId=be8acc1f8ad55fb93b4f170e17533329">Join Prime Video Channels Free Trial</a><img src="//ir-na.amazon-adsystem.com/e/ir?t=lezpress-20&l=pf4&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />',
			),
		);

		$the_ad = $amazon_ads[ $format ][ array_rand( $amazon_ads[ $format ] ) ];
		return $the_ad;
	}

}

new LWTV_Affiliate_Amazon();
