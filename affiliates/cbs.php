<?php
/**
 * Name: Affiliate Code for Paramount + (formerly CBS)
 * Description: Automagical affiliate things
 *
 * https://app.impact.com/login.user
 *
 */


class LWTV_Affiliate_CBS {
	public function show_ads( $post_id, $format = 'wide' ) {
		$the_ad = self::output_widget( $post_id, $format );
		return $the_ad;
	}

	/**
	 * Figure out which ad we really want to show....
	 */
	public function output_widget( $post_id, $format ) {

		// banner - 728x90
		// thin   - 160x600
		// tiny   - 320x50
		// wide   - 300x250
		$generic_array = array(
			'banner' => array(
				'<a href="https://paramountplus.qflm.net/c/1242493/1025279/3065" target="_top" id="1025279"><img src="//a.impactradius-go.com/display-ad/3065-1025279" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1025279/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1025009/3065" target="_top" id="1025009"><img src="//a.impactradius-go.com/display-ad/3065-1025009" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1025009/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1021253/3065" target="_top" id="1021253"><img src="//a.impactradius-go.com/display-ad/3065-1021253" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1021253/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'thin'   => array(
				'<a href="https://paramountplus.qflm.net/c/1242493/1021249/3065" target="_top" id="1021249"><img src="//a.impactradius-go.com/display-ad/3065-1021249" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1021249/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1025275/3065" target="_top" id="1025275"><img src="//a.impactradius-go.com/display-ad/3065-1025275" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1025275/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1025005/3065" target="_top" id="1025005"><img src="//a.impactradius-go.com/display-ad/3065-1025005" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1025005/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'tiny'   => array(
				'<a href="https://paramountplus.qflm.net/c/1242493/1025278/3065" target="_top" id="1025278"><img src="//a.impactradius-go.com/display-ad/3065-1025278" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1025278/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1025008/3065" target="_top" id="1025008"><img src="//a.impactradius-go.com/display-ad/3065-1025008" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1025008/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1021252/3065" target="_top" id="1021252"><img src="//a.impactradius-go.com/display-ad/3065-1021252" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1021252/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'wide'   => array(
				'<a href="https://paramountplus.qflm.net/c/1242493/1025276/3065" target="_top" id="1025276"><img src="//a.impactradius-go.com/display-ad/3065-1025276" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1025276/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1025006/3065" target="_top" id="1025006"><img src="//a.impactradius-go.com/display-ad/3065-1025006" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1025006/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1021250/3065" target="_top" id="1021250"><img src="//a.impactradius-go.com/display-ad/3065-1021250" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1021250/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
		);

		if ( 'text' === $format ) {
			$the_ad = '<h3 id="1007327"><a href="https://paramountplus.qflm.net/c/1242493/1007327/3065">The most captivating stories and experiences are on Paramount+. Try it free!</a></h3><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1007327/3065" style="position:absolute;visibility:hidden;" border="0" />';
		} else {
			// Else, generic.
			$the_ad = $generic_array[ $format ][ array_rand( $generic_array[ $format ] ) ];
		}
		return $the_ad;
	}
}

new LWTV_Affiliate_CBS();
