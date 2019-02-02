<?php
/**
 * Name: Affiliate Code for CBS
 * Description: Automagical affiliate things
 */


class LWTV_Affiliate_CBS {
	public static function show_ads( $post_id, $type, $format = 'wide' ) {

		// Return the proper output
		switch ( $type ) {
			case 'text':
				$the_ad = self::output_text( $post_id );
				break;
			case 'widget':
			default:
				$the_ad = self::output_widget( $post_id, $format );
				break;
		}

		return $the_ad;
	}

	/**
	 * CBS wants to run specific ad copy, so we're doing this to try theirs...
	 */
	public static function output_text( $post_id ) {

		$slug        = get_post_field( 'post_name', $post_id );
		$named_array = array(
			'blue-bloods'                   => '379693',
			'macgyver'                      => '379705',
			'madam-secretary'               => '379710',
			'ncis'                          => '379721',
			'ncis-new-orleans'              => '379721',
			'star-trek-the-next-generation' => '366250',
			'star-trek-deep-space-nine'     => '366250',
			'star-trek-discovery'           => '440479',
			'the-big-bang-theory'           => '359935',
			'the-good-wife'                 => '456231',
			'the-good-fight'                => '455992',
			'the-young-and-the-restless'    => '359948',
		);

		if ( array_key_exists( $slug, $named_array ) ) {
			$link = $named_array[ $slug ];
		} else {
			$link = '176086';
		}

		return $link;
	}

	/**
	 * Figure out which ad we really want to show....
	 */
	public static function output_widget( $post_id, $format ) {

		// Wide Ads
		$named_array = array(
			// 300x250
			'wide' => array(
				'the-good-wife'                 => '456231',
				'the-good-fight'                => '485771',
				'star-trek-the-next-generation' => '366250',
				'star-trek-deep-space-nine'     => '366250',
				'star-trek-discovery'           => '440479',
				'macgyver'                      => '379705',
				'madam-secretary'               => '379710',
				'ncis'                          => '379721',
				'ncis-new-orleans'              => '379721',
				'the-young-and-the-restless'    => '359948',
				'blue-bloods'                   => '379693',
				'the-big-bang-theory'           => '359935',
			),
			// 160x600
			'thin' => array(
				'the-good-wife'                 => '455991',
				'the-good-fight'                => '455991',
				'star-trek-the-next-generation' => '366255',
				'star-trek-deep-space-nine'     => '447371',
				'star-trek-discovery'           => '567408',
				'macgyver'                      => '379704',
				'madam-secretary'               => '379709',
				'ncis'                          => '359962',
				'ncis-new-orleans'              => '359962',
				'the-young-and-the-restless'    => '359947',
				'blue-bloods'                   => '379692',
				'the-big-bang-theory'           => '359962',
			),
		);
		$generic_array = array(
			'wide' => array( '455992', '572335', '440479', '567410', '379710', '553291' ),
			'thin' => array( '553290', '455991', '440473', '440478', '447371', '567408', '366255', '455991', '572333' ),
		);

		$slug = get_post_field( 'post_name', $post_id );

		if ( array_key_exists( $slug, $named_array[ $format ] ) ) {
			$ad = $named_array[ $format ][ $slug ];
		} else {
			$ad = $generic_array[ $format ][ array_rand( $generic_array[ $format ] ) ];
		}

		$size = ( 'wide' === $format ) ? 'width="300" height="250"' : 'width="160" height="600"';

		$the_ad = '<a href="//cbs-allaccess.7eer.net/c/1242493/' . $ad . '/3065"><img src="//a.impactradius-go.com/display-ad/3065-' . $ad . '" border="0" alt="" ' . $size . ' /></a><img height="0" width="0" src="//cbs-allaccess.7eer.net/i/1242493/' . $ad . '/3065" style="position:absolute;visibility:hidden;" border="0" />';

		return $the_ad;

	}

}

new LWTV_Affiliate_CBS();
