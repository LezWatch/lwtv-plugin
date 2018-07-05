<?php
/**
 * Name: Affiliate Code for Amazon
 * Description: Auto Ads in Amazon Affiliate Code
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

// If the file can't be found, bail.
if ( ! file_exists( WP_CONTENT_DIR . '/library/assets/ApaiIO/vendor/autoload.php' ) ) {
	return;
}

// Call the API
require_once WP_CONTENT_DIR . '/library/assets/ApaiIO/vendor/autoload.php';

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
			if ( WP_DEBUG ) {
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
			'showtime'    => array(
				'banner'   => '1TAG79C3PQ0GFXZ39R82',
				'linkid'   => '7a8637a378a920f5283cfa96508e4976',
				'category' => 'primevideochannels',
			),
			'starz'       => array(
				'banner'   => '0GJR9JYSTS77TVQ2EH82',
				'linkid'   => '6944b61eb373fdddc4cea0a94de193d7',
				'category' => 'primevideochannels',
			),
			'hbo'         => array(
				'banner'   => '1E0AR7ZBTK5HEDE0CM82',
				'linkid'   => '5ae0b3481ac50cc4239cc2040980b290',
				'category' => 'primevideochannels',
			),
			'bbc-america' => array(
				'banner'   => '06V9DZJBZ21E92635K82',
				'linkid'   => '21313d1cf4a1e7763f97e3ff8159ad93',
				'category' => 'primevideochannels',
			),
			'bbc-four'    => array(
				'banner'   => '06V9DZJBZ21E92635K82',
				'linkid'   => '21313d1cf4a1e7763f97e3ff8159ad93',
				'category' => 'primevideochannels',
			),
			'bbc-three'   => array(
				'banner'   => '06V9DZJBZ21E92635K82',
				'linkid'   => '21313d1cf4a1e7763f97e3ff8159ad93',
				'category' => 'primevideochannels',
			),
			'bbc-two'     => array(
				'banner'   => '06V9DZJBZ21E92635K82',
				'linkid'   => '21313d1cf4a1e7763f97e3ff8159ad93',
				'category' => 'primevideochannels',
			),
			'bbc-one'     => array(
				'banner'   => '06V9DZJBZ21E92635K82',
				'linkid'   => '21313d1cf4a1e7763f97e3ff8159ad93',
				'category' => 'primevideochannels',
			),
			'bbc-wales'   => array(
				'banner'   => '06V9DZJBZ21E92635K82',
				'linkid'   => '21313d1cf4a1e7763f97e3ff8159ad93',
				'category' => 'primevideochannels',
			),
			'itv'         => array(
				'banner'   => '06V9DZJBZ21E92635K82',
				'linkid'   => '21313d1cf4a1e7763f97e3ff8159ad93',
				'category' => 'primevideochannels',
			),
			'itv-encore'  => array(
				'banner'   => '06V9DZJBZ21E92635K82',
				'linkid'   => '21313d1cf4a1e7763f97e3ff8159ad93',
				'category' => 'primevideochannels',
			),
		);

		// If the network is one of the networks above, let's set that:
		$stations = get_the_terms( $post_id, 'lez_stations' );
		if ( $stations && ! is_wp_error( $stations ) ) {
			foreach ( $stations as $station ) {
				if ( array_key_exists( $station->slug, $networks ) ) {
					$the_ad = $networks[ $station->slug ];
				}
			}
		}

		// The bounties if nothing else applies
		$bounties = array(
			'primeent'   => array(
				'expires'  => 'ongoing',
				'banner'   => '167KTXY4JXQWA6K8A502',
				'linkid'   => '897bd791c62bbe1c38f7403ddadf695e',
				'category' => 'primeent',
			),
			'primeent-2' => array(
				'expires'  => 'ongoing',
				'banner'   => '167KTXY4JXQWA6K8A502',
				'linkid'   => 'bd40942602aeae4fee219182dbc67a45',
				'category' => 'primeent',
			),
			'primemain'  => array(
				'expires'  => 'ongoing',
				'banner'   => '1MDTME9E9G651CJTDA82',
				'linkid'   => 'd2554a8e2e75fb1dd2efa7a835f4a182',
				'category' => 'primemain',
			),
			'firetv'     => array(
				'expires'  => 'ongoing',
				'banner'   => '1HZB17ZSN14HN5F95FG2',
				'linkid'   => '36cbf51ed866322a693cdccd1a86b56c',
				'category' => 'firetv',
			),
			'dvd-anime'  => array(
				'expires'  => 'ongoing',
				'banner'   => '0EAD0FBRPQ3YQD8YDM82',
				'linkid'   => '1b5814d9b0df1553d2cdc3c331696759',
				'category' => 'dvd',
			),
		);

		// If bounty isn't set yet, we need to here
		if ( ! isset( $the_ad ) ) {
			// Exclude anything expired
			foreach ( $bounties as $a_bounty => $value ) {
				$expires = strtotime( $value['expires'] );

				if ( 'ongoing' === $value['expires'] || $expires >= time() ) {
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
