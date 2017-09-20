<?php
/**
 * Name: Amazon Affiliate Code
 * Description: Auto Adds in Amazon Affiliate Code
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
 *
 * @since 1.0
 */

class LWTV_Amazon {
	
	/**
	 * show_amazon function.
	 *
	 * @access public
	 * @param mixed $content
	 * @return void
	 */
	public static function show_amazon( $post_id ) {

		$setKeywords  = '';
		$use_fallback = false;
		$results      = array();
		$setCategory  = 'DVD';

		// Check if the transient expired or not.
		if ( false === get_transient( 'lezwatchtv_amazon_affiliates' ) ) {
			set_transient( 'lezwatchtv_amazon_affiliates', 'check_amazon', 59 );
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
		
		// Return the output
		return self::output( $use_fallback, $results );;
	}

	/**
	 * output function.
	 * 
	 * @access public
	 * @param bool $use_fallback (default: false)
	 * @param array $results (default: array())
	 * @return void
	 */
	public static function output( $use_fallback = false, $results = array() ) {
		echo '<center>';
		if ( !$use_fallback ) {
			$top_items = array_slice( $results['Items']['Item'], 0, 2 );
			foreach ( $top_items as $item ) {
				if ( is_array( $item ) && array_key_exists( 'ASIN', $item ) ) {
					?>
					<iframe style="width:120px;height:240px;" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" src="//ws-na.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&OneJS=1&Operation=GetAdHtml&MarketPlace=US&source=ac&ref=tf_til&ad_type=product_link&tracking_id=lezpress-20&marketplace=amazon&region=US&placement=<?php echo $item['ASIN']; ?>&asins=<?php echo $item['ASIN']; ?>&show_border=true&link_opens_in_new_window=true&price_color=333333&title_color=0066C0&bg_color=FFFFFF"></iframe>&nbsp;
					<?php
				}
			}
			echo '<p><a href="' . $results['Items']['MoreSearchResultsUrl'] . '" target="_blank">More Results ... </a></p>';
		} else {
			// Nothing was related enough, show the default
			echo do_shortcode( '[amazon-bounties]' );
		}
		echo '</center>';
	}
	
}

new LWTV_Amazon();