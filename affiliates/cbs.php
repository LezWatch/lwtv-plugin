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
			'the-good-wife'                 => array(
				'banner' => '456314',
				'thin'   => '455991',
				'tiny'   => '456233',
				'wide'   => '456231',
			),
			'the-good-fight'                => array(
				'banner' => '553296',
				'thin'   => '455991',
				'tiny'   => '456233',
				'wide'   => '485771',
			),
			'star-trek-the-next-generation' => array(
				'banner' => '366253',
				'thin'   => '366255',
				'tiny'   => '440481',
				'wide'   => '366250',
			),
			'star-trek-deep-space-nine'     => array(
				'banner' => '447375',
				'thin'   => '447371',
				'tiny'   => '447374',
				'wide'   => '366250',
			),
			'star-trek-discovery'           => array(
				'banner' => '440477',
				'thin'   => '567408',
				'tiny'   => '440476',
				'wide'   => '440479',
			),
			'macgyver'                      => array(
				'banner' => '379708',
				'thin'   => '379704',
				'tiny'   => '379707',
				'wide'   => '379705',
			),
			'madam-secretary'               => array(
				'banner' => '379713',
				'thin'   => '379709',
				'tiny'   => '379712',
				'wide'   => '379710',
			),
			'the-young-and-the-restless'    => array(
				'banner' => '359951',
				'thin'   => '359947',
				'tiny'   => '359950',
				'wide'   => '359948',
			),
			'blue-bloods'                   => array(
				'banner' => '379690',
				'thin'   => '379692',
				'tiny'   => '379689',
				'wide'   => '379693',
			),
		);

		$generic_array = array(
			'banner' => array( '359951', '379713', '379690', '553296', '447375', '359957', '366253', '359946', '359941', '440477', '379708', '359938', '456314', '447376' ),
			'thin'   => array( '359947', '379709', '553290', '455991', '440473', '440478', '447371', '567408', '366255', '455991', '447371', '572333', '379704', '379692', '359962' ),
			'tiny'   => array( '359950', '379712', '379689', '379707', '359960', '447374', '456233', '440481', '440476', '553293' ),
			'wide'   => array( '359948', '379710', '359935', '379721', '455992', '572335', '440479', '567410', '379710', '379693', '379705', '456231', '366250', '485771', '553291' ),
		);

		if ( 'text' === $format ) {
			$the_ad = '<h3><a href="//cbs-allaccess.7eer.net/c/1242493/304293/3065">Stream your favorite shows on demand -- now commercial free -- on CBS All Access.  Learn more now!</a></h3>
<img height="0" width="0" src="//cbs-allaccess.7eer.net/i/1242493/304293/3065" style="position:absolute;visibility:hidden;" border="0" />';
		} else {
			$slug = get_post_field( 'post_name', $post_id );

			if ( array_key_exists( $slug, $named_array ) ) {
				$ad = $named_array[ $slug ][ $format ];
			} else {
				$ad = $generic_array[ $format ][ array_rand( $generic_array[ $format ] ) ];
			}

			$sizes = explode( 'x', LWTV_Affilliates::$format_sizes[ $format ] );
			$size  = 'width="' . $sizes[0] . '" height="' . $sizes[1] . '"';

			$the_ad = '<a href="//cbs-allaccess.7eer.net/c/1242493/' . $ad . '/3065"><img src="//a.impactradius-go.com/display-ad/3065-' . $ad . '" border="0" alt="" ' . $size . ' /></a><img height="0" width="0" src="//cbs-allaccess.7eer.net/i/1242493/' . $ad . '/3065" style="position:absolute;visibility:hidden;" border="0" />';
		}

		return $the_ad;

	}

}

new LWTV_Affiliate_CBS();
