<?php
/**
 * Name: Affiliate Code for CBS
 * Description: Automagical affiliate things
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

		// expire - optional but formatted DD-MM-YYYY
		// banner - 728x90
		// thin   - 160x600
		// tiny   - 320x50
		// wide   - 300x250
		$named_array = array(
			'the-good-wife'                 => array(
				'banner' => '<a href="https://cbsallaccess.qflm.net/c/1242493/825534/3065" id="825534"><img src="//a.impactradius-go.com/display-ad/3065-825534" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/825534/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/825526/3065" id="825526"><img src="//a.impactradius-go.com/display-ad/3065-825526" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/825526/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/825531/3065" id="825531"><img src="//a.impactradius-go.com/display-ad/3065-825531" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/825531/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/825528/3065" id="825528"><img src="//a.impactradius-go.com/display-ad/3065-825528" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/825528/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'the-good-fight'                => array(
				'banner' => '<a href="https://cbsallaccess.qflm.net/c/1242493/829238/3065" id="829238"><img src="//a.impactradius-go.com/display-ad/3065-829238" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/829238/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/829232/3065" id="829232"><img src="//a.impactradius-go.com/display-ad/3065-829232" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/829232/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/829235/3065" id="829235"><img src="//a.impactradius-go.com/display-ad/3065-829235" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/829235/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/829233/3065" id="829233"><img src="//a.impactradius-go.com/display-ad/3065-829233" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/829233/3065" style="position:absolute;visibility:hidden;" border="0" />',
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
			'star-trek-picard'           => array(
				'banner' => '<a href="https://cbsallaccess.qflm.net/c/1242493/817737/3065" id="817737"><img src="//a.impactradius-go.com/display-ad/3065-817737" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/817737/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/817740/3065" id="817740"><img src="//a.impactradius-go.com/display-ad/3065-817740" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/817740/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/817733/3065" id="817733"><img src="//a.impactradius-go.com/display-ad/3065-817733" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/817733/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/817741/3065" id="817741"><img src="//a.impactradius-go.com/display-ad/3065-817741" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/817741/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'macgyver'                      => array(
				'banner' => '<a href="https://cbsallaccess.qflm.net/c/1242493/765516/3065" id="765516"><img src="//a.impactradius-go.com/display-ad/3065-765516" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/765516/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/765512/3065" id="765512"><img src="//a.impactradius-go.com/display-ad/3065-765512" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/765512/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/765515/3065" id="765515"><img src="//a.impactradius-go.com/display-ad/3065-765515" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/765515/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/765513/3065" id="765513"><img src="//a.impactradius-go.com/display-ad/3065-765513" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/765513/3065" style="position:absolute;visibility:hidden;" border="0" />',
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
				'<a href="https://cbsallaccess.qflm.net/c/1242493/824216/3065" id="824216"><img src="//a.impactradius-go.com/display-ad/3065-824216" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/824216/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/820991/3065" id="820991"><img src="//a.impactradius-go.com/display-ad/3065-820991" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/820991/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/618685/3065" id="618685"><img src="//a.impactradius-go.com/display-ad/3065-618685" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/618685/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/553296/3065" id="553296"><img src="//a.impactradius-go.com/display-ad/3065-553296" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/553296/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'thin'   => array(
				'<a href="https://cbsallaccess.qflm.net/c/1242493/824212/3065" id="824212"><img src="//a.impactradius-go.com/display-ad/3065-824212" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/824212/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/820987/3065" id="820987"><img src="//a.impactradius-go.com/display-ad/3065-820987" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/820987/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/798686/3065" id="798686"><img src="//a.impactradius-go.com/display-ad/3065-798686" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/798686/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'tiny'   => array(
				'<a href="https://cbsallaccess.qflm.net/c/1242493/366252/3065" id="366252"><img src="//a.impactradius-go.com/display-ad/3065-366252" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/366252/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/379712/3065" id="379712"><img src="//a.impactradius-go.com/display-ad/3065-379712" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379712/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/359960/3065" id="359960"><img src="//a.impactradius-go.com/display-ad/3065-359960" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/359960/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/359956/3065" id="359956"><img src="//a.impactradius-go.com/display-ad/3065-359956" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/359956/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/359945/3065" id="359945"><img src="//a.impactradius-go.com/display-ad/3065-359945" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/359945/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/553293/3065" id="553293"><img src="//a.impactradius-go.com/display-ad/3065-553293" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/553293/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/765811/3065" id="765811"><img src="//a.impactradius-go.com/display-ad/3065-765811" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/765811/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'wide'   => array(
				'<a href="https://cbsallaccess.qflm.net/c/1242493/829233/3065" id="829233"><img src="//a.impactradius-go.com/display-ad/3065-829233" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/829233/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/825528/3065" id="825528"><img src="//a.impactradius-go.com/display-ad/3065-825528" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/825528/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/824213/3065" id="824213"><img src="//a.impactradius-go.com/display-ad/3065-824213" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/824213/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/820988/3065" id="820988"><img src="//a.impactradius-go.com/display-ad/3065-820988" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/820988/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/817741/3065" id="817741"><img src="//a.impactradius-go.com/display-ad/3065-817741" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/817741/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/798690/3065" id="798690"><img src="//a.impactradius-go.com/display-ad/3065-798690" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/798690/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
		);

		if ( 'text' === $format ) {
			$the_ad = '<h3 id="553299"><a href="//cbsallaccess.qflm.net/c/1242493/553299/3065">Watch groundbreaking original series now when you try 1 week FREE of CBS All Access! </a></h3><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/553299/3065" style="position:absolute;visibility:hidden;" border="0" />';
		} else {
			$slug = get_post_field( 'post_name', $post_id );

			if ( array_key_exists( $slug, $named_array ) ) {
				$the_ad = $named_array[ $slug ][ $format ];
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
