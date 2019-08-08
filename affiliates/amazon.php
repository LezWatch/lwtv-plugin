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

		// 300x250
		$wide_ads = array(
			'video_channels' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=amazonvideosubs&banner=1ZM55MXES3N9G71ATN82&f=ifr&lc=pf4&linkID=ef20c0d245d5dd2b2330717533a4620d&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			'prime_music'    => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primemusic&banner=0Y451P54C03XJ9ZRPK82&f=ifr&lc=pf4&linkID=d78c4343df9c6b7f7d1f11eca3d2ce2d&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			'gleason'        => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=primeent&banner=167KTXY4JXQWA6K8A502&f=ifr&lc=pf4&linkID=22cc9ec1a96c2ac2e1b7f98fcfd66b08&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
		);

		// 160x600
		$thin_ads = array(
			'mrs_maisel'      => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primeent&banner=179M3M4HZVSRN11NY602&f=ifr&linkID=53e0bfd2c41b215bcced8ea64b0ba29e&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			'free_trial'      => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primemain&banner=161E7D8SBJFWK4M9MNG2&f=ifr&linkID=e44b5b49165dffff0775bdcc19b8837e&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			'game_of_thrones' => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primevideochannels&banner=1KRDJGQFJG64G8WX9VG2&f=ifr&linkID=13a641a9725b9152ebfabbe71a63d600&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			'showtime'        => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primevideochannels&banner=0ETV0M1DQEFGJ83G8M02&f=ifr&linkID=3d40187e7e6e92edda113937f7f0351f&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			'cbs_all_access'  => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primevideochannels&banner=1RSA96WAH3YGF1HZHCG2&f=ifr&linkID=03f4dc485374936e354fa2e0a0631211&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			'hbo'             => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primevideochannels&banner=0TZTZKMZNERPRPZYXN82&f=ifr&linkID=708dd271d014cf55bc7896f3c869f4ce&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			'britbox'         => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primevideochannels&banner=062FRX60FAE1CTM89V82&f=ifr&linkID=1706413e37b21dd457a799ddd6c21cfa&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
			'westworld'       => '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primevideochannels&banner=068G1JD09XF3D3H7QXG2&f=ifr&linkID=b98208f39f5f81f57d6d42710877c5b8&t=lezpress-20&tracking_id=lezpress-20" width="160" height="600" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>',
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
			case 'thin':
				$the_ad = $amazon_ads['thin'][ array_rand( $amazon_ads['thin'] ) ];
				break;
			case 'wide':
				$the_ad = $amazon_ads['wide'][ array_rand( $amazon_ads['wide'] ) ];
				break;
		}

		return $the_ad;
	}

}

new LWTV_Affiliate_Amazon();
