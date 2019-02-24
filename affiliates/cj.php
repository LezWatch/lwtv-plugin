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
	public static function show_ads( $post_id, $network, $format = 'wide' ) {
		$the_ad = self::output_widget( $post_id, $network, $format );
		return $the_ad;
	}

	public static function output_widget( $post_id, $network, $format ) {

		$wide_array = array(
			'amc'   => array(
				'<a href="http://www.tkqlhce.com/click-8811547-13354376" target="_top">
<img src="http://www.ftjcfx.com/image-8811547-13354376" width="300" height="250" alt="" border="0"/></a>',
				'<a href="http://www.anrdoezrs.net/click-8811547-13411304" target="_top">
<img src="http://www.lduhtrp.net/image-8811547-13411304" width="300" height="250" alt="" border="0"/></a>',
				'<a href="http://www.tkqlhce.com/click-8811547-13320550" target="_top">
<img src="http://www.awltovhc.com/image-8811547-13320550" width="300" height="250" alt="" border="0"/></a>',
				'<a href="http://www.tkqlhce.com/click-8811547-13320552" target="_top">
<img src="http://www.lduhtrp.net/image-8811547-13320552" width="300" height="250" alt="" border="0"/></a>',
			),
			'starz' => array(
				'<a href="http://www.tkqlhce.com/click-8811547-13538859" target="_top">
<img src="http://www.ftjcfx.com/image-8811547-13538859" width="300" height="250" alt="" border="0"/></a>',
				'<a href="http://www.jdoqocy.com/click-8811547-13538806" target="_top">
<img src="http://www.ftjcfx.com/image-8811547-13538806" width="300" height="250" alt="" border="0"/></a>',
				'<a href="http://www.kqzyfj.com/click-8811547-13504354" target="_top">
<img src="http://www.ftjcfx.com/image-8811547-13504354" width="300" height="250" alt="" border="0"/></a>',
				'<a href="http://www.kqzyfj.com/click-8811547-13426486" target="_top">
<img src="http://www.lduhtrp.net/image-8811547-13426486" width="300" height="250" alt="" border="0"/></a>',
			),
		);

		switch ( $format ) {
			case 'wide':
				$the_ad = $wide_array[ $network ][ array_rand( $wide_array[ $network ] ) ];
				break;
			case 'tiny':
				$the_ad = '<a href="http://www.jdoqocy.com/click-8811547-13569917" target="_top">
<img src="http://www.lduhtrp.net/image-8811547-13569917" width="320" height="50" alt="" border="0"/></a>';
				break;
			default:
				// currently blank.
				$the_ad = '';
		}

		return $the_ad;

	}

}

new LWTV_Affiliate_CJ();
