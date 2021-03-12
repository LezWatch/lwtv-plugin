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
				'<a href="https://paramountplus.qflm.net/c/1242493/1006755/3065" target="_top" id="1006755"><img src="//a.impactradius-go.com/display-ad/3065-1006755" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006755/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006724/3065" target="_top" id="1006724"><img src="//a.impactradius-go.com/display-ad/3065-1006724" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006724/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006711/3065" target="_top" id="1006711"><img src="//a.impactradius-go.com/display-ad/3065-1006711" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006711/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006700/3065" target="_top" id="1006700"><img src="//a.impactradius-go.com/display-ad/3065-1006700" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006700/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006673/3065" target="_top" id="1006673"><img src="//a.impactradius-go.com/display-ad/3065-1006673" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006673/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006480/3065" target="_top" id="1006480"><img src="//a.impactradius-go.com/display-ad/3065-1006480" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006480/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006474/3065" target="_top" id="1006474"><img src="//a.impactradius-go.com/display-ad/3065-1006474" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006474/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006467/3065" target="_top" id="1006467"><img src="//a.impactradius-go.com/display-ad/3065-1006467" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006467/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006460/3065" target="_top" id="1006460"><img src="//a.impactradius-go.com/display-ad/3065-1006460" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006460/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006455/3065" target="_top" id="1006455"><img src="//a.impactradius-go.com/display-ad/3065-1006455" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006455/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006415/3065" target="_top" id="1006415"><img src="//a.impactradius-go.com/display-ad/3065-1006415" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006415/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006388/3065" target="_top" id="1006388"><img src="//a.impactradius-go.com/display-ad/3065-1006388" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006388/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'thin'   => array(
				'<a href="https://paramountplus.qflm.net/c/1242493/1006748/3065" target="_top" id="1006748"><img src="//a.impactradius-go.com/display-ad/3065-1006748" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006748/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006717/3065" target="_top" id="1006717"><img src="//a.impactradius-go.com/display-ad/3065-1006717" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006717/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006717/3065" target="_top" id="1006717"><img src="//a.impactradius-go.com/display-ad/3065-1006717" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006717/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006704/3065" target="_top" id="1006704"><img src="//a.impactradius-go.com/display-ad/3065-1006704" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006704/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006693/3065" target="_top" id="1006693"><img src="//a.impactradius-go.com/display-ad/3065-1006693" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006693/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006666/3065" target="_top" id="1006666"><img src="//a.impactradius-go.com/display-ad/3065-1006666" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006666/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'tiny'   => array(
				'<a href="https://paramountplus.qflm.net/c/1242493/1006752/3065" target="_top" id="1006752"><img src="//a.impactradius-go.com/display-ad/3065-1006752" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006752/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006721/3065" target="_top" id="1006721"><img src="//a.impactradius-go.com/display-ad/3065-1006721" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006721/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006708/3065" target="_top" id="1006708"><img src="//a.impactradius-go.com/display-ad/3065-1006708" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006708/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006697/3065" target="_top" id="1006697"><img src="//a.impactradius-go.com/display-ad/3065-1006697" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006697/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006670/3065" target="_top" id="1006670"><img src="//a.impactradius-go.com/display-ad/3065-1006670" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006670/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'wide'   => array(
				'<a href="https://paramountplus.qflm.net/c/1242493/1006750/3065" target="_top" id="1006750"><img src="//a.impactradius-go.com/display-ad/3065-1006750" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006750/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006719/3065" target="_top" id="1006719"><img src="//a.impactradius-go.com/display-ad/3065-1006719" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006719/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006706/3065" target="_top" id="1006706"><img src="//a.impactradius-go.com/display-ad/3065-1006706" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006706/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006695/3065" target="_top" id="1006695"><img src="//a.impactradius-go.com/display-ad/3065-1006695" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006695/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006668/3065" target="_top" id="1006668"><img src="//a.impactradius-go.com/display-ad/3065-1006668" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006668/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006495/3065" target="_top" id="1006495"><img src="//a.impactradius-go.com/display-ad/3065-1006495" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006495/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006478/3065" target="_top" id="1006478"><img src="//a.impactradius-go.com/display-ad/3065-1006478" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006478/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006472/3065" target="_top" id="1006472"><img src="//a.impactradius-go.com/display-ad/3065-1006472" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006472/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006458/3065" target="_top" id="1006458"><img src="//a.impactradius-go.com/display-ad/3065-1006458" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006458/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006453/3065" target="_top" id="1006453"><img src="//a.impactradius-go.com/display-ad/3065-1006453" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006453/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006411/3065" target="_top" id="1006411"><img src="//a.impactradius-go.com/display-ad/3065-1006411" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006411/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://paramountplus.qflm.net/c/1242493/1006386/3065" target="_top" id="1006386"><img src="//a.impactradius-go.com/display-ad/3065-1006386" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://paramountplus.qflm.net/i/1242493/1006386/3065" style="position:absolute;visibility:hidden;" border="0" />',
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
