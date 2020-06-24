<?php
/**
 * Name: Affiliate Code for Local
 * Description: Basically this is us :D
 */

class LWTV_Affiliate_Local {

	public function show_ads( $post_id, $format = 'wide' ) {
		$the_ad = self::output_widget( $post_id, $format );
		return $the_ad;
	}

	public function output_widget( $post_id, $format ) {
		$ad_array = array(
			'facetwp'    => array(
				'name' => 'FacetWP',
				'url'  => 'https://facetwp.com/?ref=91&campaign=LezPress',
			),
			'dreampress' => array(
				'name' => 'DreamPress',
				'url'  => 'https://www.dreamhost.com/r.cgi?1354424/wordpress/',
			),
			'yikes'      => array(
				'name' => 'Yikes',
				'url'  => 'https://www.yikesinc.com',
			),
		);
		$one_item = array_rand( $ad_array );

		if ( 'text' === $format ) {
			$the_ad = '<a href="' . $ad_array[ $one_item ]['url'] . '">' . $ad_array[ $one_item ]['name'] . '</a>';
		} else {
			$image  = 'images/' . esc_attr( $one_item ) . '-' . LWTV_Affilliates::$format_sizes[ $format ] . '.png';
			$the_ad = '<a href="' . $ad_array[ $one_item ]['url'] . '"><img src="' . plugins_url( $image, __FILE__ ) . '"></a>';
		}

		return $the_ad;
	}

}

new LWTV_Affiliate_Local();
