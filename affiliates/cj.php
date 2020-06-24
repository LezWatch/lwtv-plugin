<?php
/**
 * Name: Affiliate Code for Commision Junction
 * Description: Automagical affiliate things
 */


class LWTV_Affiliate_CJ {
	/**
	 * Generic Call Ads
	 * @param  int     $post_id Post ID
	 * @param  string  $network Network name (i.e. starz, amc)
	 * @param  string  $format  Ad Format (Default Wide)
	 * @return string           The ad itself
	 */
	public function show_ads( $post_id, $network, $format = 'wide' ) {
		$the_ad = self::output_widget( $post_id, $network, $format );
		return $the_ad;
	}

	public function output_widget( $post_id, $network, $format ) {

		$wide_array = array(
			'amc'   => array(
				'<a href="https://www.tkqlhce.com/click-8811547-13411308" target="_top"><img src="https://www.ftjcfx.com/image-8811547-13411308" width="300" height="250" alt="" border="0"/></a>',
				'<a href="https://www.anrdoezrs.net/click-8811547-13411321" target="_top"><img src="https://www.ftjcfx.com/image-8811547-13411321" width="300" height="250" alt="" border="0"/></a>',
				'<a href="https://www.jdoqocy.com/click-8811547-13397579" target="_top"><img src="https://www.ftjcfx.com/image-8811547-13397579" width="300" height="250" alt="" border="0"/></a>',
			),
			'starz' => array(
				'<a href="https://www.anrdoezrs.net/click-8811547-13504354" target="_top"><img src="https://www.tqlkg.com/image-8811547-13504354" width="300" height="250" alt="" border="0"/></a>',
				'<a href="https://www.anrdoezrs.net/click-8811547-13426486" target="_top"><img src="https://www.tqlkg.com/image-8811547-13426486" width="300" height="250" alt="" border="0"/></a>',
				'<a href="https://www.anrdoezrs.net/click-8811547-13651051" target="_top"><img src="https://www.awltovhc.com/image-8811547-13651051" width="300" height="250" alt="" border="0"/></a>',
			),
		);

		$tiny_array = array(
			'<a href="https://www.dpbolvw.net/click-8811547-13651041" target="_top"><img src="https://www.ftjcfx.com/image-8811547-13651041" width="300" height="50" alt="" border="0"/></a>',
			'<a href="https://www.anrdoezrs.net/click-8811547-13504348" target="_top"><img src="https://www.ftjcfx.com/image-8811547-13504348" width="728" height="90" alt="" border="0"/></a>',
			'<a href="https://www.tkqlhce.com/click-8811547-13426485" target="_top"><img src="https://www.ftjcfx.com/image-8811547-13426485" width="468" height="60" alt="" border="0"/></a>',
		);

		switch ( $format ) {
			case 'wide':
				$the_ad = $wide_array[ $network ][ array_rand( $wide_array[ $network ] ) ];
				break;
			case 'tiny':
				$the_ad = $tiny_array[ array_rand( $tiny_array ) ];
				break;
			default:
				// currently blank.
				$the_ad = '';
		}

		return $the_ad;

	}

}

new LWTV_Affiliate_CJ();
