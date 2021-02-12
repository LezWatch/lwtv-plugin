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
			'star-trek'                  => array(
				'banner' => '<a href="https://cbsallaccess.qflm.net/c/1242493/920745/3065" target="_top" id="920745"><img src="//a.impactradius-go.com/display-ad/3065-920745" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/920745/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/920739/3065" target="_top" id="920739"><img src="//a.impactradius-go.com/display-ad/3065-920739" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/920739/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/920742/3065" target="_top" id="920742"><img src="//a.impactradius-go.com/display-ad/3065-920742" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/920742/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/920740/3065" target="_top" id="920740"><img src="//a.impactradius-go.com/display-ad/3065-920740" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/920740/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'danger-force'                  => array(
				'banner' => '<a href="https://cbsallaccess.qflm.net/c/1242493/993924/3065" target="_top" id="993924"><img src="//a.impactradius-go.com/display-ad/3065-993924" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/993924/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'thin'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/993920/3065" target="_top" id="993920"><img src="//a.impactradius-go.com/display-ad/3065-993920" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/993920/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'tiny'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/993923/3065" target="_top" id="993923"><img src="//a.impactradius-go.com/display-ad/3065-993923" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/993923/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'wide'   => '<a href="https://cbsallaccess.qflm.net/c/1242493/993921/3065" target="_top" id="993921"><img src="//a.impactradius-go.com/display-ad/3065-993921" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/993921/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
		);

		$generic_array = array(
			'banner' => array(
				'<a href="https://cbsallaccess.qflm.net/c/1242493/884363/3065" target="_top" id="884363"><img src="//a.impactradius-go.com/display-ad/3065-884363" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/884363/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/887229/3065" target="_top" id="887229"><img src="//a.impactradius-go.com/display-ad/3065-887229" border="0" alt="" width="728" height="90"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/887229/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'thin'   => array(
				'<a href="https://cbsallaccess.qflm.net/c/1242493/884358/3065" target="_top" id="884358"><img src="//a.impactradius-go.com/display-ad/3065-884358" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/884358/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/887224/3065" target="_top" id="887224"><img src="//a.impactradius-go.com/display-ad/3065-887224" border="0" alt="" width="160" height="600"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/887224/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'tiny'   => array(
				'<a href="https://cbsallaccess.qflm.net/c/1242493/884361/3065" target="_top" id="884361"><img src="//a.impactradius-go.com/display-ad/3065-884361" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/884361/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/887227/3065" target="_top" id="887227"><img src="//a.impactradius-go.com/display-ad/3065-887227" border="0" alt="" width="320" height="50"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/887227/3065" style="position:absolute;visibility:hidden;" border="0" />',
			),
			'wide'   => array(
				'<a href="https://cbsallaccess.qflm.net/c/1242493/884359/3065" target="_top" id="884359"><img src="//a.impactradius-go.com/display-ad/3065-884359" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/884359/3065" style="position:absolute;visibility:hidden;" border="0" />',
				'<a href="https://cbsallaccess.qflm.net/c/1242493/887225/3065" target="_top" id="887225"><img src="//a.impactradius-go.com/display-ad/3065-887225" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="https://cbsallaccess.qflm.net/i/1242493/887225/3065" style="position:absolute;visibility:hidden;" border="0" />',
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
