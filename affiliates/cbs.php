<?php
/**
 * Name: Affiliate Code for CBS
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

		// expire - optional but formatted DD-MM-YYYY
		// banner - 728x90
		// thin   - 160x600
		// tiny   - 320x50
		// wide   - 300x250
		$named_array = array(
			'macgyver'                   => array(
				'banner' => '<a href="https://cbsallaccess.qflm.net/c/1242493/379708/3065" id="379708"><img src="//a.impactradius-go.com/display-ad/3065-379708" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379708/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/379704/3065" id="379704"><img src="//a.impactradius-go.com/display-ad/3065-379704" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379704/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/379707/3065" id="379707"><img src="//a.impactradius-go.com/display-ad/3065-379707" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379707/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/379705/3065" id="379705"><img src="//a.impactradius-go.com/display-ad/3065-379705" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379705/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'madam-secretary'            => array(
				'banner' => '<a href="https://cbsallaccess.qflm.net/c/1242493/379713/3065" id="379713"><img src="//a.impactradius-go.com/display-ad/3065-379713" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379713/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/379709/3065" id="379709"><img src="//a.impactradius-go.com/display-ad/3065-379709" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379709/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/379712/3065" id="379712"><img src="//a.impactradius-go.com/display-ad/3065-379712" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379712/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/379710/3065" id="379710"><img src="//a.impactradius-go.com/display-ad/3065-379710" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/379710/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'star-trek'                  => array(
				'banner' => '<a href="https://cbsallaccess.qflm.net/c/1242493/567414/3065" id="567414"><img src="//a.impactradius-go.com/display-ad/3065-567414" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/567414/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/366255/3065" id="366255"><img src="//a.impactradius-go.com/display-ad/3065-366255" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/366255/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/567409/3065" id="567409"><img src="//a.impactradius-go.com/display-ad/3065-567409" border="0" alt="" width="300" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/567409/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/567410/3065" id="567410"><img src="//a.impactradius-go.com/display-ad/3065-567410" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/567410/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'the-young-and-the-restless' => array(
				'banner' => '<a href="https://cbsallaccess.qflm.net/c/1242493/359951/3065" id="359951"><img src="//a.impactradius-go.com/display-ad/3065-359951" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/359951/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/359947/3065" id="359947"><img src="//a.impactradius-go.com/display-ad/3065-359947" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/359947/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/359950/3065" id="359950"><img src="//a.impactradius-go.com/display-ad/3065-359950" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/359950/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/359948/3065" id="359948"><img src="//a.impactradius-go.com/display-ad/3065-359948" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/359948/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
		);

		$generic_array = array(
			'banner' => array(
				'<a href="https://cbsallaccess.qflm.net/c/1242493/884363/3065" target="_top" id="884363"><img src="//a.impactradius-go.com/display-ad/3065-884363" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/884363/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/887229/3065" target="_top" id="887229"><img src="//a.impactradius-go.com/display-ad/3065-887229" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/887229/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/901631/3065" target="_top" id="901631"><img src="//a.impactradius-go.com/display-ad/3065-901631" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/901631/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'thin'   => array(
				'<a href="https://cbsallaccess.qflm.net/c/1242493/884358/3065" target="_top" id="884358"><img src="//a.impactradius-go.com/display-ad/3065-884358" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/884358/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/887224/3065" target="_top" id="887224"><img src="//a.impactradius-go.com/display-ad/3065-887224" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/887224/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/901627/3065" target="_top" id="901627"><img src="//a.impactradius-go.com/display-ad/3065-901627" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/901627/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'tiny'   => array(
				'<a href="https://cbsallaccess.qflm.net/c/1242493/884361/3065" target="_top" id="884361"><img src="//a.impactradius-go.com/display-ad/3065-884361" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/884361/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/887227/3065" target="_top" id="887227"><img src="//a.impactradius-go.com/display-ad/3065-887227" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/887227/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/901630/3065" target="_top" id="901630"><img src="//a.impactradius-go.com/display-ad/3065-901630" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/901630/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'wide'   => array(
				'<a href="https://cbsallaccess.qflm.net/c/1242493/884359/3065" target="_top" id="884359"><img src="//a.impactradius-go.com/display-ad/3065-884359" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/884359/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/887225/3065" target="_top" id="887225"><img src="//a.impactradius-go.com/display-ad/3065-887225" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/887225/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/901628/3065" target="_top" id="901628"><img src="//a.impactradius-go.com/display-ad/3065-901628" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/901628/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
		);

		if ( 'text' === $format ) {
			$the_ad = '<h3 id="553299"><a href="https://cbsallaccess.qflm.net/c/1242493/553299/3065">Watch groundbreaking original series now when you try 1 week FREE of CBS All Access! </a></h3><img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/553299/3065" style="position:absolute;visibility:hidden;" border="0" />';
		} else {
			// Check if the slug is in the $named_array, and use that.
			// Else, generic.
			$slug = get_post_field( 'post_name', $post_id );

			// If the slug CONTAINS Star Trek, it's star-trek
			if ( strpos( $slug, 'star-trek' ) ) {
				$slug = 'star-trek';
			}

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
