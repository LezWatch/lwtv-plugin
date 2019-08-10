<?php
/**
 * Name: Affiliate Code for CBS
 * Description: Automagical affiliate things
 */


class LWTV_Affiliate_CBS {
	public static function show_ads( $post_id, $format = 'wide' ) {
		$the_ad = self::output_widget( $post_id, $format );
		return $the_ad;
	}

	/**
	 * Figure out which ad we really want to show....
	 */
	public static function output_widget( $post_id, $format ) {
		$named_array = array(
			// Disabled because the ads are gone.
			'the-good-wife'                 => array(
				'id'     => '584613',
				'banner' => '<a href="//CBS-AllAccess.qflm.net/c/1242493/591249/3065"><img src="//a.impactradius-go.com/display-ad/3065-591249" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/591249/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/591244/3065"><img src="//a.impactradius-go.com/display-ad/3065-591244" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/591244/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/591247/3065"><img src="//a.impactradius-go.com/display-ad/3065-591247" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/591247/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/591245/3065"><img src="//a.impactradius-go.com/display-ad/3065-591245" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/591245/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'the-good-fight'                => array(
				'id'     => '558753',
				'banner' => '<a href="//CBS-AllAccess.qflm.net/c/1242493/586322/3065"><img src="//a.impactradius-go.com/display-ad/3065-586322" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/586322/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/611856/3065"><img src="//a.impactradius-go.com/display-ad/3065-611856" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/611856/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/586325/3065"><img src="//a.impactradius-go.com/display-ad/3065-586325" border="0" alt="" width="970" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/586325/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/586318/3065"><img src="//a.impactradius-go.com/display-ad/3065-586318" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/586318/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'star-trek-the-next-generation' => array(
				'id'     => '645227',
				'banner' => '<a href="//CBS-AllAccess.qflm.net/c/1242493/366253/3065"><img src="//a.impactradius-go.com/display-ad/3065-366253" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/366253/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/601087/3065"><img src="//a.impactradius-go.com/display-ad/3065-601087" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/601087/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/366249/3065"><img src="//a.impactradius-go.com/display-ad/3065-366249" border="0" alt="" width="1200" height="627"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/366249/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/366250/3065"><img src="//a.impactradius-go.com/display-ad/3065-366250" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/366250/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'star-trek-deep-space-nine'     => array(
				'id'     => '644851',
				'banner' => '<a href="//CBS-AllAccess.qflm.net/c/1242493/366253/3065"><img src="//a.impactradius-go.com/display-ad/3065-366253" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/366253/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/366255/3065"><img src="//a.impactradius-go.com/display-ad/3065-366255" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/366255/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/366249/3065"><img src="//a.impactradius-go.com/display-ad/3065-366249" border="0" alt="" width="1200" height="627"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/366249/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/366248/3065"><img src="//a.impactradius-go.com/display-ad/3065-366248" border="0" alt="" width="1080" height="1080"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/366248/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'star-trek-discovery'           => array(
				'id'     => '601079',
				'banner' => '<a href="//CBS-AllAccess.qflm.net/c/1242493/618683/3065"><img src="//a.impactradius-go.com/display-ad/3065-618683" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/618683/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/618683/3065"><img src="//a.impactradius-go.com/display-ad/3065-618683" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/618683/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/618689/3065"><img src="//a.impactradius-go.com/display-ad/3065-618689" border="0" alt="" width="1600" height="500"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/618689/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/567410/3065"><img src="//a.impactradius-go.com/display-ad/3065-567410" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/567410/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'macgyver'                      => array(
				'id'     => '176088',
				'banner' => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379708/3065"><img src="//a.impactradius-go.com/display-ad/3065-379708" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379708/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379704/3065"><img src="//a.impactradius-go.com/display-ad/3065-379704" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379704/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379707/3065"><img src="//a.impactradius-go.com/display-ad/3065-379707" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379707/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379705/3065"><img src="//a.impactradius-go.com/display-ad/3065-379705" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379705/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'madam-secretary'               => array(
				'id'     => '176090',
				'banner' => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379713/3065"><img src="//a.impactradius-go.com/display-ad/3065-379713" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379713/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379709/3065"><img src="//a.impactradius-go.com/display-ad/3065-379709" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379709/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379712/3065"><img src="//a.impactradius-go.com/display-ad/3065-379712" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379712/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379710/3065"><img src="//a.impactradius-go.com/display-ad/3065-379710" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379710/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'the-young-and-the-restless'    => array(
				'id'     => '176089',
				'banner' => '<a href="//CBS-AllAccess.qflm.net/c/1242493/359951/3065"><img src="//a.impactradius-go.com/display-ad/3065-359951" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359951/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/359947/3065"><img src="//a.impactradius-go.com/display-ad/3065-359947" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359947/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/359950/3065"><img src="//a.impactradius-go.com/display-ad/3065-359950" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359950/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/359948/3065"><img src="//a.impactradius-go.com/display-ad/3065-359948" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359948/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'blue-bloods'                   => array(
				'id'     => '176092',
				'banner' => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379690/3065"><img src="//a.impactradius-go.com/display-ad/3065-379690" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379690/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379692/3065"><img src="//a.impactradius-go.com/display-ad/3065-379692" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379692/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379689/3065"><img src="//a.impactradius-go.com/display-ad/3065-379689" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379689/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//CBS-AllAccess.qflm.net/c/1242493/379693/3065"><img src="//a.impactradius-go.com/display-ad/3065-379693" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379693/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
		);

		$generic_array = array(
			'banner' => array(
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/359957/3065"><img src="//a.impactradius-go.com/display-ad/3065-359957" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359957/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/366253/3065"><img src="//a.impactradius-go.com/display-ad/3065-366253" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/366253/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/379713/3065"><img src="//a.impactradius-go.com/display-ad/3065-379713" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379713/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/553296/3065"><img src="//a.impactradius-go.com/display-ad/3065-553296" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/553296/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/586322/3065"><img src="//a.impactradius-go.com/display-ad/3065-586322" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/586322/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/591249/3065"><img src="//a.impactradius-go.com/display-ad/3065-591249" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/591249/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/617345/3065"><img src="//a.impactradius-go.com/display-ad/3065-617345" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/617345/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/614515/3065"><img src="//a.impactradius-go.com/display-ad/3065-614515" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/614515/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/634084/3065"><img src="//a.impactradius-go.com/display-ad/3065-634084" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/634084/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'thin'   => array(
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/591244/3065"><img src="//a.impactradius-go.com/display-ad/3065-591244" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/591244/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/611856/3065"><img src="//a.impactradius-go.com/display-ad/3065-611856" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/611856/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/617340/3065"><img src="//a.impactradius-go.com/display-ad/3065-617340" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/617340/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/614511/3065"><img src="//a.impactradius-go.com/display-ad/3065-614511" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/614511/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/634080/3065"><img src="//a.impactradius-go.com/display-ad/3065-634080" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/634080/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/359934/3065"><img src="//a.impactradius-go.com/display-ad/3065-359934" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359934/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/359947/3065"><img src="//a.impactradius-go.com/display-ad/3065-359947" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359947/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/366255/3065"><img src="//a.impactradius-go.com/display-ad/3065-366255" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/366255/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/553290/3065"><img src="//a.impactradius-go.com/display-ad/3065-553290" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/553290/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/567408/3065"><img src="//a.impactradius-go.com/display-ad/3065-567408" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/567408/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'tiny'   => array(
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/359960/3065"><img src="//a.impactradius-go.com/display-ad/3065-359960" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359960/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/359956/3065"><img src="//a.impactradius-go.com/display-ad/3065-359956" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359956/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/359945/3065"><img src="//a.impactradius-go.com/display-ad/3065-359945" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359945/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/359940/3065"><img src="//a.impactradius-go.com/display-ad/3065-359940" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359940/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/553293/3065"><img src="//a.impactradius-go.com/display-ad/3065-553293" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/553293/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/567412/3065"><img src="//a.impactradius-go.com/display-ad/3065-567412" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/567412/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/591247/3065"><img src="//a.impactradius-go.com/display-ad/3065-591247" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/591247/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/611859/3065"><img src="//a.impactradius-go.com/display-ad/3065-611859" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/611859/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/616476/3065"><img src="//a.impactradius-go.com/display-ad/3065-616476" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/616476/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/617343/3065"><img src="//a.impactradius-go.com/display-ad/3065-617343" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/617343/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'wide'   => array(
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/359935/3065"><img src="//a.impactradius-go.com/display-ad/3065-359935" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/359935/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/366250/3065"><img src="//a.impactradius-go.com/display-ad/3065-366250" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/366250/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/379710/3065"><img src="//a.impactradius-go.com/display-ad/3065-379710" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/379710/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/553291/3065"><img src="//a.impactradius-go.com/display-ad/3065-553291" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/553291/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/567410/3065"><img src="//a.impactradius-go.com/display-ad/3065-567410" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/567410/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/586318/3065"><img src="//a.impactradius-go.com/display-ad/3065-586318" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/586318/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/591245/3065"><img src="//a.impactradius-go.com/display-ad/3065-591245" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/591245/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/611857/3065"><img src="//a.impactradius-go.com/display-ad/3065-611857" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/611857/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/614512/3065"><img src="//a.impactradius-go.com/display-ad/3065-614512" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/614512/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/617341/3065"><img src="//a.impactradius-go.com/display-ad/3065-617341" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/617341/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//CBS-AllAccess.qflm.net/c/1242493/634081/3065"><img src="//a.impactradius-go.com/display-ad/3065-634081" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//CBS-AllAccess.qflm.net/i/1242493/634081/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
		);

		if ( 'text' === $format ) {
			$the_ad = '<h3><a href="//cbs-allaccess.7eer.net/c/1242493/304293/3065">Stream your favorite shows on demand -- now commercial free -- on CBS All Access.  Learn more now!</a></h3>
<img height="0" width="0" src="//cbs-allaccess.7eer.net/i/1242493/304293/3065" style="position:absolute;visibility:hidden;" border="0" />';
		} else {
			$slug = get_post_field( 'post_name', $post_id );

			if ( array_key_exists( $slug, $named_array ) ) {
				$the_ad = $named_array[ $slug ][ $format ];
			} else {
				$the_ad = $generic_array[ $format ][ array_rand( $generic_array[ $format ] ) ];
			}
		}
		return $the_ad;
	}
}

new LWTV_Affiliate_CBS();
