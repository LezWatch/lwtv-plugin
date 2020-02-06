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

		// expire - optional but formated DD-MM-YYYY
		// banner - 728x90
		// thin   - 160x600
		// tiny   - 320x50
		// wide   - 300x250
		$named_array = array(
			'tommy'                         => array(
				'banner' => '<a href="//cbsallaccess.qflm.net/c/1242493/764131/3065" id="764131"><img src="//a.impactradius-go.com/display-ad/3065-764131" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/764131/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//cbsallaccess.qflm.net/c/1242493/764127/3065" id="764127"><img src="//a.impactradius-go.com/display-ad/3065-764127" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/764127/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//cbsallaccess.qflm.net/c/1242493/764130/3065" id="764130"><img src="//a.impactradius-go.com/display-ad/3065-764130" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/764130/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//cbsallaccess.qflm.net/c/1242493/764128/3065" id="764128"><img src="//a.impactradius-go.com/display-ad/3065-764128" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/764128/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'the-good-wife'                 => array(
				'banner' => '<a href="//cbsallaccess.qflm.net/c/1242493/586322/3065" id="586322"><img src="//a.impactradius-go.com/display-ad/3065-586322" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/586322/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//cbsallaccess.qflm.net/c/1242493/611856/3065" id="611856"><img src="//a.impactradius-go.com/display-ad/3065-611856" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/611856/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//cbsallaccess.qflm.net/c/1242493/611859/3065" id="611859"><img src="//a.impactradius-go.com/display-ad/3065-611859" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/611859/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//cbsallaccess.qflm.net/c/1242493/611857/3065" id="611857"><img src="//a.impactradius-go.com/display-ad/3065-611857" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/611857/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'the-good-fight'                => array(
				'banner' => '<a href="//cbsallaccess.qflm.net/c/1242493/591249/3065" id="591249"><img src="//a.impactradius-go.com/display-ad/3065-591249" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/591249/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//cbsallaccess.qflm.net/c/1242493/591244/3065" id="591244"><img src="//a.impactradius-go.com/display-ad/3065-591244" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/591244/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//cbsallaccess.qflm.net/c/1242493/591247/3065" id="591247"><img src="//a.impactradius-go.com/display-ad/3065-591247" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/591247/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//cbsallaccess.qflm.net/c/1242493/591245/3065" id="591245"><img src="//a.impactradius-go.com/display-ad/3065-591245" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/591245/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'star-trek-the-next-generation' => array(
				'banner' => '<a href="//cbsallaccess.qflm.net/c/1242493/366253/3065" id="366253"><img src="//a.impactradius-go.com/display-ad/3065-366253" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/366253/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//cbsallaccess.qflm.net/c/1242493/366255/3065" id="366255"><img src="//a.impactradius-go.com/display-ad/3065-366255" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/366255/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//cbsallaccess.qflm.net/c/1242493/366252/3065" id="366252"><img src="//a.impactradius-go.com/display-ad/3065-366252" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/366252/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//cbsallaccess.qflm.net/c/1242493/366250/3065" id="366250"><img src="//a.impactradius-go.com/display-ad/3065-366250" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/366250/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'star-trek-deep-space-nine'     => array(
				'banner' => '<a href="//cbsallaccess.qflm.net/c/1242493/366253/3065" id="366253"><img src="//a.impactradius-go.com/display-ad/3065-366253" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/366253/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//cbsallaccess.qflm.net/c/1242493/618680/3065" id="618680"><img src="//a.impactradius-go.com/display-ad/3065-618680" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/618680/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//cbsallaccess.qflm.net/c/1242493/366252/3065" id="366252"><img src="//a.impactradius-go.com/display-ad/3065-366252" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/366252/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//cbsallaccess.qflm.net/c/1242493/366250/3065" id="366250"><img src="//a.impactradius-go.com/display-ad/3065-366250" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/366250/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'star-trek-discovery'           => array(
				'banner' => '<a href="//cbsallaccess.qflm.net/c/1242493/567414/3065" id="567414"><img src="//a.impactradius-go.com/display-ad/3065-567414" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/567414/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//cbsallaccess.qflm.net/c/1242493/567408/3065" id="567408"><img src="//a.impactradius-go.com/display-ad/3065-567408" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/567408/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//cbsallaccess.qflm.net/c/1242493/618683/3065" id="618683"><img src="//a.impactradius-go.com/display-ad/3065-618683" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/618683/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//cbsallaccess.qflm.net/c/1242493/618681/3065" id="618681"><img src="//a.impactradius-go.com/display-ad/3065-618681" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/618681/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'macgyver'                      => array(
				'banner' => '<a href="//cbsallaccess.qflm.net/c/1242493/379708/3065" id="379708"><img src="//a.impactradius-go.com/display-ad/3065-379708" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379708/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//cbsallaccess.qflm.net/c/1242493/379704/3065" id="379704"><img src="//a.impactradius-go.com/display-ad/3065-379704" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379704/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//cbsallaccess.qflm.net/c/1242493/379707/3065" id="379707"><img src="//a.impactradius-go.com/display-ad/3065-379707" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379707/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//cbsallaccess.qflm.net/c/1242493/379705/3065" id="379705"><img src="//a.impactradius-go.com/display-ad/3065-379705" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379705/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'madam-secretary'               => array(
				'banner' => '<a href="//cbsallaccess.qflm.net/c/1242493/379713/3065" id="379713"><img src="//a.impactradius-go.com/display-ad/3065-379713" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379713/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//cbsallaccess.qflm.net/c/1242493/379709/3065" id="379709"><img src="//a.impactradius-go.com/display-ad/3065-379709" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379709/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//cbsallaccess.qflm.net/c/1242493/379712/3065" id="379712"><img src="//a.impactradius-go.com/display-ad/3065-379712" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379712/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//cbsallaccess.qflm.net/c/1242493/379710/3065" id="379710"><img src="//a.impactradius-go.com/display-ad/3065-379710" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379710/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'the-young-and-the-restless'    => array(
				'banner' => '<a href="//cbsallaccess.qflm.net/c/1242493/359951/3065" id="359951"><img src="//a.impactradius-go.com/display-ad/3065-359951" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/359951/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//cbsallaccess.qflm.net/c/1242493/359947/3065" id="359947"><img src="//a.impactradius-go.com/display-ad/3065-359947" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/359947/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//cbsallaccess.qflm.net/c/1242493/359950/3065" id="359950"><img src="//a.impactradius-go.com/display-ad/3065-359950" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/359950/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//cbsallaccess.qflm.net/c/1242493/359948/3065" id="359948"><img src="//a.impactradius-go.com/display-ad/3065-359948" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/359948/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'blue-bloods'                   => array(
				'banner' => '<a href="//cbsallaccess.qflm.net/c/1242493/691509/3065" id="691509"><img src="//a.impactradius-go.com/display-ad/3065-691509" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/691509/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="//cbsallaccess.qflm.net/c/1242493/691506/3065" id="691506"><img src="//a.impactradius-go.com/display-ad/3065-691506" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/691506/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="//cbsallaccess.qflm.net/c/1242493/691508/3065" id="691508"><img src="//a.impactradius-go.com/display-ad/3065-691508" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/691508/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="//cbsallaccess.qflm.net/c/1242493/379693/3065" id="379693"><img src="//a.impactradius-go.com/display-ad/3065-379693" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379693/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
		);

		$generic_array = array(
			'banner' => array(
				'<a href="//cbsallaccess.qflm.net/c/1242493/764131/3065" id="764131"><img src="//a.impactradius-go.com/display-ad/3065-764131" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/764131/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/614515/3065" id="614515"><img src="//a.impactradius-go.com/display-ad/3065-614515" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/614515/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/553296/3065" id="553296"><img src="//a.impactradius-go.com/display-ad/3065-553296" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/553296/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/618685/3065" id="618685"><img src="//a.impactradius-go.com/display-ad/3065-618685" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/618685/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/611861/3065" id="611861"><img src="//a.impactradius-go.com/display-ad/3065-611861" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/611861/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/591249/3065" id="591249"><img src="//a.impactradius-go.com/display-ad/3065-591249" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/591249/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'thin'   => array(
				'<a href="//cbsallaccess.qflm.net/c/1242493/764127/3065" id="764127"><img src="//a.impactradius-go.com/display-ad/3065-764127" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/764127/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/618680/3065" id="618680"><img src="//a.impactradius-go.com/display-ad/3065-618680" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/618680/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/614511/3065" id="614511"><img src="//a.impactradius-go.com/display-ad/3065-614511" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/614511/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/611856/3065" id="611856"><img src="//a.impactradius-go.com/display-ad/3065-611856" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/611856/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/591244/3065" id="591244"><img src="//a.impactradius-go.com/display-ad/3065-591244" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/591244/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/553290/3065" id="553290"><img src="//a.impactradius-go.com/display-ad/3065-553290" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/553290/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'tiny'   => array(
				'<a href="//cbsallaccess.qflm.net/c/1242493/764130/3065" id="764130"><img src="//a.impactradius-go.com/display-ad/3065-764130" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/764130/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/691508/3065" id="691508"><img src="//a.impactradius-go.com/display-ad/3065-691508" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/691508/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/618683/3065" id="618683"><img src="//a.impactradius-go.com/display-ad/3065-618683" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/618683/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/614514/3065" id="614514"><img src="//a.impactradius-go.com/display-ad/3065-614514" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/614514/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/611859/3065" id="611859"><img src="//a.impactradius-go.com/display-ad/3065-611859" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/611859/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/591247/3065" id="591247"><img src="//a.impactradius-go.com/display-ad/3065-591247" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/591247/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/567412/3065" id="567412"><img src="//a.impactradius-go.com/display-ad/3065-567412" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/567412/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'wide'   => array(
				'<a href="//cbsallaccess.qflm.net/c/1242493/764128/3065" id="764128"><img src="//a.impactradius-go.com/display-ad/3065-764128" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/764128/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/618681/3065" id="618681"><img src="//a.impactradius-go.com/display-ad/3065-618681" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/618681/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/614512/3065" id="614512"><img src="//a.impactradius-go.com/display-ad/3065-614512" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/614512/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/611857/3065" id="611857"><img src="//a.impactradius-go.com/display-ad/3065-611857" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/611857/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/591245/3065" id="591245"><img src="//a.impactradius-go.com/display-ad/3065-591245" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/591245/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="//cbsallaccess.qflm.net/c/1242493/553291/3065" id="553291"><img src="//a.impactradius-go.com/display-ad/3065-553291" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/553291/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
		);

		if ( 'text' === $format ) {
			$the_ad = '<h3 id="553299"><a href="//cbsallaccess.qflm.net/c/1242493/553299/3065">Watch groundbreaking original series now when you try 1 week FREE of CBS All Access! </a></h3><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/553299/3065" style="position:absolute;visibility:hidden;" border="0" />';
		} else {
			$slug = get_post_field( 'post_name', $post_id );

			if ( array_key_exists( $slug, $named_array ) ) {
				$expires   = ( array_key_exists( 'expire', $named_array[ $slug ] ) ) ? strtotime( $named_array[ $slug ]['expire'] ) : '';
				$timezone  = 'America/New_York';
				$timestamp = time();
				$date_time = new DateTime( 'now', new DateTimeZone( $timezone ) ); //first argument "must" be a string
				$date_time->setTimestamp( $timestamp ); //adjust the object to correct timestamp
				$today     = $date_time->format( 'm-d-Y' );

				if ( ! isset( $expires ) || ( isset( $expires ) && $today !== $expires ) ) {
					$the_ad = $named_array[ $slug ][ $format ];
				}
			}

			// if nothing is set...
			if ( ! isset( $the_ad ) ) {
				$the_ad = $generic_array[ $format ][ array_rand( $generic_array[ $format ] ) ];
			}
		}
		return $the_ad;
	}
}

new LWTV_Affiliate_CBS();
