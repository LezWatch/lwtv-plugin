<?php
/**
 * Name: Affiliate Code for Amazon
 * Description: Auto Ads in Amazon Affiliate Code
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

// If the file can't be found, fall back to an affiliate.
if ( ! class_exists( 'ApaiIO\ApaiIO' ) ) {
	if ( is_archive() ) {
		$the_ad = LWTV_Affilliates::widget_affiliates( 'thin' );
	} else {
		$the_ad = LWTV_Affilliates::widget_affiliates( 'wide' );
	}
	return $the_ad;
}

use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Search;
use ApaiIO\ApaiIO;

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
	 * Check if there are some amazon videos we can direct link to...
	 */
	public static function check_amazon( $post_id, $use_fallback = false ) {

		$set_keywords = '';
		$use_bounty   = false;
		$results      = array();
		$set_category = 'DVD';

		// If there's no transient, set it for 10 minutes (5 seconds when dev mode).
		$amazon_transient = get_transient( 'lezwatchtv_amazon_affiliates' );
		if ( false === ( $amazon_transient ) ) {
			$checktime = ( HOUR_IN_SECONDS / 6 );
			if ( defined( 'LWTV_DEV_SITE' ) && LWTV_DEV_SITE ) {
				$checktime = 5;
			}
			set_transient( 'lezwatchtv_amazon_affiliates', 'check_amazon', $checktime );
		} else {
			// If there's a transient, we need to limit how many calls we make
			// else Amazon gets narky with us.
			$use_bounty = true;
		}

		// If it's a show and there's no fallback flag...
		if ( is_singular( 'post_type_shows' ) && ! $use_bounty ) {
			// Add Title for shows
			$set_keywords .= get_the_title();

			// If the station is Amazon, then we change the category
			$stations = get_the_terms( $post_id, 'lez_stations' );
			if ( $stations && ! is_wp_error( $stations ) ) {
				foreach ( $stations as $station ) {
					if ( 'amazon-prime' === $station->slug ) {
						$set_category    = 'UnboxVideo';
						$set_browse_node = '16262841';
					}
				}
			}

			if ( 'UnboxVideo' !== $set_category ) {
				// Checks if the show isn't on amazon.

				// If the country isn't USA, we get insanely dumb results
				$countries = get_the_terms( $post_id, 'lez_country' );
				if ( $countries && ! is_wp_error( $countries ) ) {
					foreach ( $countries as $country ) {
						if ( 'USA' !== $country->name ) {
							$use_bounty = true;
						}
					}
				}
			}
		} elseif ( $use_fallback ) {
			// can't have both true, after all
			$use_bounty = false;
		} else {
			// If we got here, we need to use the bounty
			$use_bounty = true;
		}

		// If there are no keywords AND Fallback is false
		if ( ! empty( $set_keywords ) && ! $use_bounty ) {

			try {
				$conf    = new GenericConfiguration();
				$client  = new \GuzzleHttp\Client();
				$request = new \ApaiIO\Request\GuzzleRequest( $client );

				$conf
					->setCountry( 'com' )
					->setAccessKey( AMAZON_PRODUCT_API_KEY )
					->setSecretKey( AMAZON_PRODUCT_API_SECRET )
					->setAssociateTag( 'lezpress-20' )
					->setResponseTransformer( new \ApaiIO\ResponseTransformer\XmlToArray() )
					->setRequest( $request );
			} catch ( \Exception $e ) {
				$use_bounty = true;
			}
			$apai_io = new ApaiIO( $conf );
			$search  = new Search();
			$search->setCategory( $set_category );
			if ( isset( $set_browse_node ) ) {
				$search->setBrowseNode( $set_browse_node );
			}
			$search->setKeywords( $set_keywords );

			$results = $apai_io->runOperation( $search );

			// If we don't get a valid array, we will use the use_bounty
			if (
				! is_array( $results ) ||
				! array_key_exists( 'Item', $results['Items'] ) ||
				array_key_exists( 'Errors', $results['Items']['Request'] )
			) {
				$use_bounty = true;
			}
		} else {
			$use_bounty = true;
		}

		if ( $use_bounty ) {
			$return = 'bounty';
		} elseif ( $use_fallback ) {
			$return = 'fallback';
		} else {
			$return = $results;
		}

		return $return;
	}

	/**
	 * Output widget
	 */
	public static function output_widget( $post_id ) {

		$results = self::check_amazon( $post_id );
		$output  = '<center>';

		switch ( $results ) {
			case 'bounty':
				$output .= self::bounty( $post_id );
				break;
			case 'fallback':
				$output .= LWTV_Affilliates::apple( $post_id, 'widget' );
				break;
			default:
				$top_items = array_slice( $results['Items']['Item'], 0, 2 );
				foreach ( $top_items as $item ) {
					if ( is_array( $item ) && array_key_exists( 'ASIN', $item ) ) {
						$output .= '<iframe style="width:120px;height:240px;" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" src="//ws-na.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&OneJS=1&Operation=GetAdHtml&MarketPlace=US&source=ac&ref=tf_til&ad_type=product_link&tracking_id=lezpress-20&marketplace=amazon&region=US&placement=' . $item['ASIN'] . '&asins=' . $item['ASIN'] . '&show_border=true&link_opens_in_new_window=true&price_color=333333&title_color=0066C0&bg_color=FFFFFF"></iframe>&nbsp;';
					}
				}
				$output .= '<p><a href="' . $results['Items']['MoreSearchResultsUrl'] . '" target="_blank">More Results ... </a></p>';
				break;
		}

		$output .= '</center>';

		return $output;
	}

	/**
	 * Generate a random bounty
	 */
	public static function bounty( $post_id ) {

		// First let's check if the show is on a network we know....
		$networks = array(
			'showtime'     => array(
				'expires'  => '2018-12-30',
				'banner'   => '1E0AR7ZBTK5HEDE0CM82',
				'linkid'   => '2e2ac20186faf8b19ef4ca931b5337bd',
				'category' => 'primevideochannels',
			),
			'hbo'          => array(
				'expires'  => '2018-12-30',
				'banner'   => '15WAHRRCWADG8X4F09G2',
				'linkid'   => '74197ffde2efa3f07ef91a0ce7f2a01a',
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
		$return = '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=' . $the_ad['category'] . '&banner=' . $the_ad['banner'] . '&f=ifr&lc=pf4&linkID=' . $the_ad['linkid'] . '&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';

		return $return;
	}

}

new LWTV_Affiliate_Amazon();
