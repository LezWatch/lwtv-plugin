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
			'banner' => array(
				'included_with' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primeent&banner=0KRPDR35SJEJY56XVF82&f=ifr&linkID=f0e6f459f22e0dd0e362358394e5002a&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'watch_love'    => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primeent&banner=06GDZC4PE16KAQ62F782&f=ifr&linkID=4fe950e8c7d2ba57156a5926d4315566&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'showtime'      => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primevideochannels&banner=0TB5ZH8V2RW5ZMJ6RTG2&f=ifr&linkID=16ea90da53cfe8badb61fdb9ed738413&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'hbo'           => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primevideochannels&banner=1CS9E8J4KWXM2ZDHRP02&f=ifr&linkID=b486bbe1d9575075e76789635c41c28d&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'westworld'     => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primevideochannels&banner=0E6VNAW8MYMV7K97HM02&f=ifr&linkID=71850e396b2d61881c50bf7082ddb544&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'britbox'       => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=48&l=ur1&category=primevideochannels&banner=1PNEZB8ETQ5ZR2BQ4B02&f=ifr&linkID=e82a0dae437756969c6cccd586e67302&t=lezpress-20&tracking_id=lezpress-20" width="728" height="90" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 300x250
			'wide'   => array(
				'watch_love'    => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primeent&banner=031XMRKZ05JH88GSKT82&f=ifr&linkID=0eda6317f02eb58a18e5d06634f35ffb&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'included_with' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primeent&banner=0PDYZ1WNDGPDAT9V4SG2&f=ifr&linkID=40912dcdea0af40a882b2a1e6711bedf&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'mrs_maisel'    => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primeent&banner=19QKV2FQ54WZWV7RKN02&f=ifr&linkID=ad6a2f4cbd36f0ff78847de449207a6f&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'showtime'      => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primevideochannels&banner=0J2MREHF9PSHNP7A0R02&f=ifr&linkID=592be3e2055b664f0b2a46c5b4ead155&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'starz'         => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primevideochannels&banner=0GJR9JYSTS77TVQ2EH82&f=ifr&linkID=ef4c0cc9f7e825ab879b504dd4312814&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'hbo'           => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primevideochannels&banner=1E0AR7ZBTK5HEDE0CM82&f=ifr&linkID=642355c304db0d3fa9bd5a60acd43415&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'westworld'     => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primevideochannels&banner=150VYK260KXYESVKAA02&f=ifr&linkID=e546c685be02d7a908b27899e078b046&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			// 160x600
			'thin'   => array(
				'mrs_maisel'    => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primeent&banner=1N0G9R732K82F1HCKE02&f=ifr&linkID=b3d4407d478cc4cdcb652f1603d2bfab&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'included_with' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primeent&banner=1NDRZ5MHQWWTXZMB34G2&f=ifr&linkID=208da5848aff299733fdaa7c72f093ed&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'showtime'      => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primevideochannels&banner=17TP0K4YR94A7J9CJK82&f=ifr&linkID=ba08f4fd9ade4f711491a87baf11d5d1&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'starz'         => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primevideochannels&banner=0N482BNAZMAW3F4223G2&f=ifr&linkID=304bfffcf82c4bd04c6544929548a00d&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'hbo'           => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primevideochannels&banner=0TZTZKMZNERPRPZYXN82&f=ifr&linkID=e5ede1e635b490251099d73172c19aa4&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'westworld'     => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primevideochannels&banner=068G1JD09XF3D3H7QXG2&f=ifr&linkID=e2817b72d5bf125e6cadde458c24607c&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
				'britbox'       => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primevideochannels&banner=062FRX60FAE1CTM89V82&f=ifr&linkID=60cf21f45ab036c5bf0aa743086c078f&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			),
			'tiny'   => array(
				'shop' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=288&l=ur1&category=primeent&banner=1JYFV583HV833YKS3M82&f=ifr&linkID=18ec8f2348142042016ab12347871d9c&t=lezpress-20&tracking_id=lezpress-20" width="320" height="50" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
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
