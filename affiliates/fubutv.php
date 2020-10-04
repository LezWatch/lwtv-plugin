<?php
/**
 * Name: Affiliate Code for FubuTV
 * Description: Automagical affiliate things
 *
 * https://app.impact.com/login.user
 *
 */


class LWTV_Affiliate_FubuTV {

	public function show_ads( $post_id, $format = 'wide' ) {
		$the_ad = self::output( $post_id, $format );
		return $the_ad;
	}

	/**
	 * Figure out which ad we really want to show....
	 */
	public function output( $post_id, $format ) {

		// banner - 728x90
		// thin   - 160x600
		// tiny   - 320x50
		// wide   - 300x250

		$ad_array = array(
			'banner' => array(
				'<a href="https://www.fubo.tv/welcome?irad=581757&irmp=1242493" target="_top" id="581757"><img src="//a.impactradius-go.com/display-ad/5119-581757" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://fubotv.pxf.io/i/1242493/581757/5119" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'thin'   => array(
				'<a href="https://www.fubo.tv/welcome?irad=519848&irmp=1242493" target="_top" id="519848"><img src="//a.impactradius-go.com/display-ad/5119-519848" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://fubotv.pxf.io/i/1242493/519848/5119" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'tiny'   => array(
				'<a href="https://www.fubo.tv/welcome?irad=581755&irmp=1242493" target="_top" id="581755"><img src="//a.impactradius-go.com/display-ad/5119-581755" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://fubotv.pxf.io/i/1242493/581755/5119" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'wide'   => array(
				'<a href="https://www.fubo.tv/welcome?irad=581753&irmp=1242493" target="_top" id="581753"><img src="//a.impactradius-go.com/display-ad/5119-581753" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://fubotv.pxf.io/i/1242493/581753/5119" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://www.fubo.tv/lp/ent/?irad=365791&irmp=1242493" target="_top" id="365791"><img src="//a.impactradius-go.com/display-ad/5119-365791" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://fubotv.pxf.io/i/1242493/365791/5119" style="position:absolute;visibility:hidden;" border="0" />',
			),
		);

		$the_ad = $ad_array[ $format ][ array_rand( $ad_array[ $format ] ) ];
		return $the_ad;
	}
}

new LWTV_Affiliate_FubuTV();
