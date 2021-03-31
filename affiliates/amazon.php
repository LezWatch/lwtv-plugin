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
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ez&f=ifr&linkID=bc537c4fc9970127660d9155f3cd74ca&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=prime_up&banner=13X19G3KXJPB8R7KG782&f=ifr&linkID=695dbcfeb57ea94e290ed92cd5486900&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=prime_up&banner=0B87032Z18DPFFYKJ0R2&f=ifr&linkID=6bf745cb04e6d138b5b989d6c8f4ff50&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 300x250
			'wide'   => array(
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ez&f=ifr&linkID=34b655d0305ca006ba4092d25e938a64&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemain&banner=028WNSXDMC6H5YDNCB82&f=ifr&lc=pf4&linkID=0c5e0729bdc760bfc1bbb4c9345fb864&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primegift&banner=10BSGCHPCJG0HZN45H82&f=ifr&lc=pf4&linkID=861c5cabe1ddf72d46fc42e04ea2b230&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=prime_up&banner=0JQ3SQCZ5YZW83R39GG2&f=ifr&linkID=22ec834ad556e71fe9833970cabde70e&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=prime_up&banner=0V9SJV9M2EXY1BNF0882&f=ifr&linkID=9df86c19c54822e11d2343301161bd53&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 160x600
			'thin'   => array(
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ez&f=ifr&linkID=978acb68e5e13626b740fe90c5f099e3&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=prime_up&banner=19GK7XNMVT6MBGR7FV02&f=ifr&linkID=8679f100f218e95b0b9c0ae0b7cbac63&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=prime_up&banner=14FS3RN4YQD1CA33DAR2&f=ifr&linkID=df478264156b3b5320a5a7b6076b9b05&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 468x60
			'tiny'   => array(
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=20&l=ur1&category=prime_up&banner=0FTBVMQQ9VN71WBZ6Y02&f=ifr&linkID=041870bdae0ab2140120527f60084578&t=lezpress-20&tracking_id=lezpress-20" width="120" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=20&l=ur1&category=prime_up&banner=0E3H51BVG1YNFRBCTCR2&f=ifr&linkID=8bf51f2cabc369974089a901a0754708&t=lezpress-20&tracking_id=lezpress-20" width="120" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			'text'   => array(
				'<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=41&l=ur1&category=primediscounted&banner=0GVEDS1WZ8E5S64DHH02&f=ifr&linkID=c9850d8a50e59764955fe2ec3cbd5fa2&t=lezpress-20&tracking_id=lezpress-20" width="88" height="31" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
		);

		$the_ad = $amazon_ads[ $format ][ array_rand( $amazon_ads[ $format ] ) ];
		return $the_ad;
	}

}

new LWTV_Affiliate_Amazon();
