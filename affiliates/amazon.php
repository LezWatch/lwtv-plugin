<?php
/**
 * Name: Affiliate Code for Amazon
 * Description: Auto Ads in Amazon Affiliate Code
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Amazon
 */

class LWTV_Affiliate_Amazon {

	/**
	 * Determine what ad to show
	 */
	public static function show_ads( $post_id, $type ) {

		// Return the proper output
		switch ( $type ) {
			case 'widget':
			default:
				$the_ad = self::output_widget( $post_id );
				break;
		}

		return $the_ad;
	}

	/**
	 * Output widget
	 */
	public static function output_widget( $post_id ) {
		$output = '<center>' . self::bounty( $post_id ) . '</center>';
		return $output;
	}

	/**
	 * Generate a random bounty
	 */
	public static function bounty( $post_id ) {

		// First let's check if the show is on a network we know....
		$networks = array(
			'showtime'     => array(
				'expires'  => 'ongoing',
				'banner'   => '1TAG79C3PQ0GFXZ39R82',
				'linkid'   => '09a6d9595672f638f9f1c23689c40ef5',
				'category' => 'primevideochannels',
			),
			'history'      => array(
				'expires'  => 'ongoing',
				'banner'   => '1T7E2FGW5MFJNNJWTAR2',
				'linkid'   => 'beb013efcdac91c8f30344338a953a97',
				'category' => 'primevideochannels',
			),
			'hbo'          => array(
				'expires'  => 'ongoing',
				'banner'   => '1E0AR7ZBTK5HEDE0CM82',
				'linkid'   => '07178879ba1dd6724b738c8c1069d9de',
				'category' => 'primevideochannels',
			),
			'starz'        => array(
				'expires'  => 'ongoing',
				'banner'   => '00G3SH89QT95NWK3CX02',
				'linkid'   => '1d12f79c1b8dd96406721134df111da6',
				'category' => 'primevideochannels',
			),
			'bbc-america'  => array(
				'expires'  => '2018-12-30',
				'banner'   => '1MFVGHXVB06S23PS7Y82',
				'linkid'   => 'a99e19307fc515de0e10ed04b7f70c7a',
				'category' => 'primevideochannels',
			),
			'bbc-four'     => array(
				'expires'  => '2018-12-30',
				'banner'   => '1MFVGHXVB06S23PS7Y82',
				'linkid'   => 'a99e19307fc515de0e10ed04b7f70c7a',
				'category' => 'primevideochannels',
			),
			'bbc-three'    => array(
				'expires'  => '2018-12-30',
				'banner'   => '1MFVGHXVB06S23PS7Y82',
				'linkid'   => 'a99e19307fc515de0e10ed04b7f70c7a',
				'category' => 'primevideochannels',
			),
			'bbc-two'      => array(
				'expires'  => '2018-12-30',
				'banner'   => '1MFVGHXVB06S23PS7Y82',
				'linkid'   => 'a99e19307fc515de0e10ed04b7f70c7a',
				'category' => 'primevideochannels',
			),
			'bbc-one'      => array(
				'expires'  => '2018-12-30',
				'banner'   => '1MFVGHXVB06S23PS7Y82',
				'linkid'   => 'a99e19307fc515de0e10ed04b7f70c7a',
				'category' => 'primevideochannels',
			),
			'bbc-wales'    => array(
				'expires'  => '2018-12-30',
				'banner'   => '1MFVGHXVB06S23PS7Y82',
				'linkid'   => 'a99e19307fc515de0e10ed04b7f70c7a',
				'category' => 'primevideochannels',
			),
			'itv'          => array(
				'expires'  => '2018-12-30',
				'banner'   => '1MFVGHXVB06S23PS7Y82',
				'linkid'   => 'a99e19307fc515de0e10ed04b7f70c7a',
				'category' => 'primevideochannels',
			),
			'itv-encore'   => array(
				'expires'  => '2018-12-30',
				'banner'   => '1MFVGHXVB06S23PS7Y82',
				'linkid'   => 'a99e19307fc515de0e10ed04b7f70c7a',
				'category' => 'primevideochannels',
			),
			'sky-atlantic' => array(
				'expires'  => '2018-12-30',
				'banner'   => '1MFVGHXVB06S23PS7Y82',
				'linkid'   => 'a99e19307fc515de0e10ed04b7f70c7a',
				'category' => 'primevideochannels',
			),
			'sky-1'        => array(
				'expires'  => '2018-12-30',
				'banner'   => '1MFVGHXVB06S23PS7Y82',
				'linkid'   => 'a99e19307fc515de0e10ed04b7f70c7a',
				'category' => 'primevideochannels',
			),
		);

		// If the network is one of the networks above, let's set that:
		$stations = get_the_terms( $post_id, 'lez_stations' );
		if ( $stations && ! is_wp_error( $stations ) ) {
			foreach ( $stations as $station ) {
				if ( array_key_exists( $station->slug, $networks ) ) {
					$expires = $networks[ $station->slug ]['expires'];
					if ( 'ongoing' === $expires || strtotime( $expires ) >= time() ) {
						$the_ad = $networks[ $station->slug ];
					}
				}
			}
		}

		// The bounties if nothing else applies
		$bounties = array(
			'primeent'  => array(
				'expires'  => 'ongoing',
				'banner'   => '1NPA5510D9E368222PR2',
				'linkid'   => '8a73febb7e83741deca4ec0eb7aa3a1f',
				'category' => 'primeent',
			),
			'anime-dvd' => array(
				'expires'  => 'ongoing',
				'banner'   => '0EAD0FBRPQ3YQD8YDM82',
				'linkid'   => 'a88db93ce5bd7f8a91d7dbf41aa5d75a',
				'category' => 'dvd',
			),
			'primemain' => array(
				'expires'  => 'ongoing',
				'banner'   => '028WNSXDMC6H5YDNCB82',
				'linkid'   => '69b1c93038903293d05110a1c5397f15',
				'category' => 'primemain',
			),
			'firetv'    => array(
				'expires'  => 'ongoing',
				'banner'   => '0FXJ4RSQAHG9T84VY2R2',
				'linkid'   => '7adcc745e35b87b38324232f199497df',
				'category' => 'amzn_firetv_primepr_0918',
			),
			'firetv2'   => array(
				'expires'  => '2020-10-15',
				'banner'   => '07Z1A8KKG3NGABM31PG2',
				'linkid'   => 'bf9112f31ef9ff7bb309e14bad1cc2cd',
				'category' => 'amzn_firetv_eg_101618',
			),
		);

		// If bounty isn't set yet, we need to here
		if ( ! isset( $the_ad ) ) {
			// Exclude anything expired
			foreach ( $bounties as $a_bounty => $value ) {
				$expires = $value['expires'];

				if ( 'ongoing' === $value['expires'] || strtotime( $expires ) >= time() ) {
					$bounties[ $a_bounty ] = $value;
				}
			}
			// Pick a random valid bounty
			$the_ad = $bounties [ array_rand( $bounties ) ];
		}

		// Build the Ad
		$return = '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=' . $the_ad['category'] . '&banner=' . $the_ad['banner'] . '&f=ifr&linkID=' . $the_ad['linkid'] . '&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';

		return $return;
	}

}

new LWTV_Affiliate_Amazon();
