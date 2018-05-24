<?php
/**
 * Name: Affiliate Code for CBS
 * Description: Automagical affiliate things
 */


class LWTV_Affiliate_CBS {
	function show_ads( $post_id, $type ) {

		// Return the proper output
		switch ( $type ) {
			case "text":
				$the_ad = self::output_text( $post_id );
				break;
			case "widget":
			default:
				$the_ad = self::output_widget( $post_id );
				break;
		}

		return $the_ad;
	}

	/**
	 * CBS wants to run specific ad copy, so we're doing this to try theirs...
	 */
	function output_text( $post_id ) {

		$slug        = get_post_field( 'post_name', $post_id );
		$named_array = array( 
			'the-good-wife'                 => '456231',
			'the-good-fight'                => '455992',
			'star-trek-the-next-generation' => '366250',
			'star-trek-deep-space-nine'     => '366250',
			'star-trek-discovery'           => '440479',
			'macgyver'                      => '379705',
			'madam-secretary'               => '379710',
			'ncis'                          => '379721',
			'ncis-new-orleans'              => '379721',
			'the-young-and-the-restless'    => '359948',
		);

		if ( array_key_exists( $slug, $named_array ) ) {
			$link = $named_array[$slug];
		} else {
			$link = '176086';
		}

		$output = '<a href="https://cbs-allaccess.7eer.net/c/1242493/' . $link . '/3065" target="_blank" class="btn btn-primary">CBS All Access</a><img height="0" width="0" src="//cbs-allaccess.7eer.net/c/1242493/' . $link . '/3065" style="position:absolute;visibility:hidden;" border="0" />';
		return $output;
	}

	/**
	 * Figure out which ad we really want to show....
	 */
	function output_widget( $post_id ) {

		// The possible ads
		$named_array = array( 
			'the-good-wife'                 => '456231',
			'the-good-fight'                => '455992',
			'star-trek-the-next-generation' => '366250',
			'star-trek-deep-space-nine'     => '366250',
			'star-trek-discovery'           => '440479',
			'macgyver'                      => '379705',
			'madam-secretary'               => '379710',
			'ncis'                          => '379721',
			'ncis-new-orleans'              => '379721',
			'the-young-and-the-restless'    => '359948',
		);

		$generic_array = array( '359935', '359965', '359958', '359954', '359944', '359942', '379693', '440474' );

		$slug = get_post_field( 'post_name', $post_id );

		if ( array_key_exists( $slug, $named_array ) ) {
			$ad = $named_array[$slug];
		} else {
			$ad = $generic_array[array_rand( $generic_array )];
		}

		// Build the ad
		$the_ad = '<center><a href="//cbs-allaccess.7eer.net/c/1242493/' . $ad . '/3065"><img src="//a.impactradius-go.com/display-ad/3065-' . $ad . '" border="0" alt="" width="300" height="250"/></a><img height="0" width="0" src="//cbs-allaccess.7eer.net/i/1242493/' . $ad . '/3065" style="position:absolute;visibility:hidden;" border="0" /></center>';
		
		return $the_ad;

	}

}

new LWTV_Affiliate_CBS();