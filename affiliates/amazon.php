<?php
/**
 * Name: Affiliate Code for Amazon
 * Description: Auto Ads in Amazon Affiliate Code
 */

if ( ! defined('WPINC' ) ) die;

// If the file can't be found, bail.
if ( !file_exists( WP_CONTENT_DIR . '/library/assets/ApaiIO/vendor/autoload.php' ) ) return;

// Call the API
include_once( WP_CONTENT_DIR . '/library/assets/ApaiIO/vendor/autoload.php' );

use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Search;
use ApaiIO\ApaiIO;

/**
 * class LWTV_Amazon
 */

class LWTV_Affiliate_Amazon {

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
	 * show_amazon function.
	 *
	 * @access public
	 * @param mixed $content
	 * @return void
	 */
	public static function output_widget( $post_id ) {

		$setKeywords  = '';
		$use_fallback = false;
		$results      = array();
		$setCategory  = 'DVD';

		// If there's no transient, set it for half an hour.
		if ( false === ( $amzTransient = get_transient( 'lezwatchtv_amazon_affiliates' ) ) ) {
			set_transient( 'lezwatchtv_amazon_affiliates', 'check_amazon', ( HOUR_IN_SECONDS / 4 ) );
		} else {
			$use_fallback = true;
		}
		
		if ( is_singular( 'post_type_shows' ) && !$use_fallback ) {
			// Add Title for shows
			$setKeywords .= get_the_title();

			// If the station is Amazon, then we change the category
			$stations = get_the_terms( $post_id, 'lez_stations' );
			if ( $stations && ! is_wp_error( $stations ) ) {
				foreach ( $stations as $station ) {
					if ( $station->slug == 'amazon-prime' ) {
						$setCategory   = 'UnboxVideo';
						$setBrowseNode = '16262841';
					}
				}
			} 
			
			if ( $setCategory !== 'UnboxVideo' ) {
				// Checks if the show isn't on amazon.

				// If the country isn't USA, we get insanely dumb results
				$countries = get_the_terms( $post_id, 'lez_country' );
				if ( $countries && ! is_wp_error( $countries ) ) {
					foreach ( $countries as $country ) {
						if ( $country->name !== 'USA' ) {
							$use_fallback = true;
						}
					}
				}

			}
		} else {
			$use_fallback = true;
		}
		
		// If there are no keywords AND Fallback is false
		if ( ! empty( $setKeywords ) && !$use_fallback ) {
	
			try {
				$conf = new GenericConfiguration();
				$client = new \GuzzleHttp\Client();
				$request = new \ApaiIO\Request\GuzzleRequest($client);

				$conf
					->setCountry( 'com' )
					->setAccessKey( AMAZON_PRODUCT_API_KEY )
					->setSecretKey( AMAZON_PRODUCT_API_SECRET )
					->setAssociateTag( 'lezpress-20' )
					->setResponseTransformer( new \ApaiIO\ResponseTransformer\XmlToArray() )
					->setRequest( $request );
			} catch (\Exception $e) {
				$use_fallback = true;
			}
			$apaiIO = new ApaiIO( $conf );
		
			$search = new Search();
			$search->setCategory( $setCategory );
			if ( isset( $setBrowseNode ) ) $search->setBrowseNode( $setBrowseNode );
			$search->setKeywords( $setKeywords );
	
			$results = $apaiIO->runOperation( $search );
					
			// If we don't get a valid array, we will use the use_fallback
			if ( 
				!is_array( $results ) || 
				!array_key_exists( 'Item', $results['Items'] ) ||
				array_key_exists( 'Errors', $results['Items']['Request'] )
			) {
				$use_fallback = true;
			}
		} else {
			$use_fallback = true;
		}

		$output = '<center>';
		if ( !$use_fallback ) {
			$top_items = array_slice( $results['Items']['Item'], 0, 2 );
			foreach ( $top_items as $item ) {
				if ( is_array( $item ) && array_key_exists( 'ASIN', $item ) ) {
					$output .= '<iframe style="width:120px;height:240px;" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" src="//ws-na.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&OneJS=1&Operation=GetAdHtml&MarketPlace=US&source=ac&ref=tf_til&ad_type=product_link&tracking_id=lezpress-20&marketplace=amazon&region=US&placement=' . $item['ASIN'] . '&asins=' . $item['ASIN'] . '&show_border=true&link_opens_in_new_window=true&price_color=333333&title_color=0066C0&bg_color=FFFFFF"></iframe>&nbsp;';
				}
			}
			$output .= '<p><a href="' . $results['Items']['MoreSearchResultsUrl'] . '" target="_blank">More Results ... </a></p>';
		} else {
			// Nothing was related enough, show the default
			$output .= self::bounty();
		}
		$output .= '</center>';
		
		return $output;
	}

	/**
	 * Output Text
	 */
	function output_text( $post_id ) {

		$slug        = get_post_field( 'post_name', $post_id );
		$named_array = array( 
			'the-marvelous-mrs-maisel' => 'https://www.amazon.com/gp/offer-listing/B06VYH1DF2/ref=as_li_tl?ie=UTF8&camp=1789&creative=9325&creativeASIN=B06VYH1DF2&linkCode=am2&tag=lezpress-20&linkId=801e786b364070b102eaf9f62679ccca',
			'i-love-dick'              => 'https://www.amazon.com/gp/offer-listing/B01J77GK96/ref=as_li_tl?ie=UTF8&tag=lezpress-20&camp=1789&creative=9325&linkCode=as2&creativeASIN=B01J77GK96&linkId=9d7536404d12ac3fba9106aa6dfcd927',
			'goliath'                  => 'https://www.amazon.com/gp/product/B07CQ8W5VH/ref=as_li_tl?ie=UTF8&tag=lezpress-20&camp=1789&creative=9325&linkCode=as2&creativeASIN=B07CQ8W5VH&linkId=de40c679bbf3f58db49be859dade253f',
			'alpha-house'              => 'https://www.amazon.com/gp/product/B00KITEHUW/ref=as_li_tl?ie=UTF8&tag=lezpress-20&camp=1789&creative=9325&linkCode=as2&creativeASIN=B00KITEHUW&linkId=6c43703bd45a718655f953fd3d501fdc',
			'mozart-in-the-jungle'     => 'https://www.amazon.com/gp/offer-listing/B077XPRWY1/ref=as_li_tl?ie=UTF8&camp=1789&creative=9325&creativeASIN=B077XPRWY1&linkCode=am2&tag=lezpress-20&linkId=2f9de0320ec75fba205b1fb0aab12d1b',
			'one-mississippi'          => 'https://www.amazon.com/gp/product/B0747Z3FPD/ref=as_li_tl?ie=UTF8&tag=lezpress-20&camp=1789&creative=9325&linkCode=as2&creativeASIN=B0747Z3FPD&linkId=7609c635acd397bb754cd5f6b4b27fac',
			'transparent'              => 'https://www.amazon.com/gp/product/B00I3MMTS8/ref=as_li_tl?ie=UTF8&tag=lezpress-20&camp=1789&creative=9325&linkCode=as2&creativeASIN=B00I3MMTS8&linkId=540e504ce16befc5dae4a002ecee43d1',
			'vida'                     => 'https://www.amazon.com/gp/product/B07CRQYPD7/ref=as_li_tl?ie=UTF8&tag=lezpress-20&camp=1789&creative=9325&linkCode=as2&creativeASIN=B07CRQYPD7&linkId=94ac413bd4f68c62539a9eb0fef94106',
			'take-my-wife'             => 'https://www.amazon.com/gp/product/B01IU9EKM6/ref=as_li_tl?ie=UTF8&tag=lezpress-20&camp=1789&creative=9325&linkCode=as2&creativeASIN=B01IU9EKM6&linkId=f41eace93980c6b3c366c1e7c547eaac',
			'person-of-interest'       => 'https://www.amazon.com/gp/product/B009BJBPO6/ref=as_li_tl?ie=UTF8&tag=lezpress-20&camp=1789&creative=9325&linkCode=as2&creativeASIN=B009BJBPO6&linkId=dda9b1f22a40291b0f9a83e0a51ddcb7',
			'wynonna-earp'             => 'https://www.amazon.com/gp/product/B01F7RBHPM?ie=UTF8&tag=lezpress-20&camp=1789&linkCode=xm2&creativeASIN=B01F7RBHPM',
			'the-bold-type'            => 'https://www.amazon.com/gp/product/B07B64Z7VD/ref=as_li_tl?ie=UTF8&tag=lezpress-20&camp=1789&creative=9325&linkCode=as2&creativeASIN=B07B64Z7VD&linkId=7d3f1f443f83d60dd2683c81e9ef9732',
		);

		if ( array_key_exists( $slug, $named_array ) ) {
			$link = $named_array[$slug];
		} else {
			$link = 'https://www.amazon.com/gp/video/primesignup?ref_=assoc_tag_ph_1402131641212&_encoding=UTF8&camp=1789&creative=9325&linkCode=pf4&tag=lezpress-20&linkId=18d04cea391b96ac115d798e5bca8788';
		}

		// Build the Link
		$output = '<a href="' . $link . '" target="_new">Amazon Prime</a><img src="//ir-na.amazon-adsystem.com/e/ir?t=lezpress-20&l=pf4&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />';
		return $output;
	}

	function bounty() {

		$bounties = array( 
			'prime'         => array( 
				'expires'   => 'ongoing',
				'banner'    => '167KTXY4JXQWA6K8A502',
				'linkid'    => '897bd791c62bbe1c38f7403ddadf695e',
				'category'  => 'primeent'
				),
			'cbs-2018'      => array( 
				'expires'   => '2018-12-30',
				'banner'    => '15WAHRRCWADG8X4F09G2',
				'linkid'    => '2756b628ece9f8d661b18bc637e247ad',
				'category'  => 'primevideochannels'
				),
			'hbo-2018'      => array( 
				'expires'   => '2018-12-30',
				'banner'    => '1E0AR7ZBTK5HEDE0CM82',
				'linkid'    => '09ad7675f840cbf8a3e5588c5a8d3306',
				'category'  => 'primevideochannels'
				),
			'britbox-2018'  => array( 
				'expires'   => '2018-12-30',
				'banner'    => '06V9DZJBZ21E92635K82',
				'linkid'    => '72a2ae6cb554d90c7a8eb903ff6a878e',
				'category'  => 'primevideochannels'
				),
			'showtime-2018' => array( 
				'expires'   => '2018-12-30',
				'banner'    => '1TAG79C3PQ0GFXZ39R82',
				'linkid'    => '7bb432de00c491af4c9cd22a6b2587a8',
				'category'  => 'primevideochannels'
				),
			);

		// Exclude anything expired
		foreach ( $bounties as $a_bounty => $value ) {
			$expires = strtotime( $value['expires'] );
			
			if ( $value['expires'] == 'ongoing' || $expires >= time() ) {
				$bounties[$a_bounty] = $value;
			}
		}
		// Pick a random valid bounty
		$bounty = $bounties [ array_rand( $bounties ) ];

		// Build the Ad
		$the_ad = '<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=' . $bounty['category'] . '&banner=' . $bounty['banner'] . '&f=ifr&lc=pf4&linkID=' . $bounty['linkid'] . '&t=lezpress-20&tracking_id=lezpress-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';

		return $the_ad;

	}

}

new LWTV_Affiliate_Amazon();