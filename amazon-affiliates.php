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

		$setKeywords    = '';
		$fallback       = false;
		$setCategory    = 'DVD';
		
		// Not used yet but maybe
		$amazon_nations = array( 
			'Denmark'        => 'de',
			'USA'            => 'com',
			'United Kingdom' => 'co.uk',
			'Canada'         => 'ca',
			'France'         => 'fr',
			'Japan'          => 'co.jp',
			'Italy'          => 'it',
			'China'          => 'cn',
			'Spain'          => 'es',
			'India'          => 'in',
			'Brazil'         => 'com.br',
			'Mexico'         => 'com.mx',
			'Australia'      => 'com.au'
		);

		// Disabled becuase of idiots like "Legends of Tomorrow" who use "Action Adventure" and not "Television"
		//$setBrowseNode = '163450';
		
		if ( is_singular( 'post_type_shows' ) ) {
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
				// If there show isn't on Amazon, add genres to keywords
				$genres = get_the_terms( $post_id, 'lez_genres' );
				if ( $genres && ! is_wp_error( $genres ) ) {
					foreach ( $genres as $genre ) {
						$setKeywords .= ' ' . $genre->name;
					}
				}

				// If the country isn't USA, we get insanely dumb results
				$countries = get_the_terms( $post_id, 'lez_country' );
				if ( $countries && ! is_wp_error( $countries ) ) {
					foreach ( $countries as $country ) {
						if ( $country->name !== 'USA' ) {
							//$setKeywords .= ' ' . $country->name;
							$fallback = true;
						}
					}
				}

			}
		} else {
			$fallback = true;
		}
		
		// If there are no keywords AND Fallback is false
		if ( ! empty( $setKeywords ) && !$fallback ) {
	
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
				$fallback = true;
			}
			$apaiIO = new ApaiIO( $conf );
		
			$search = new Search();
			$search->setCategory( $setCategory );
			if ( isset( $setBrowseNode ) ) $search->setBrowseNode( $setBrowseNode );
			if ( isset( $setActor ) )      $search->setActor( $setActor );
			$search->setKeywords( $setKeywords );
	
			$results = $apaiIO->runOperation( $search );
					
			// If we don't get a valid array, we will use the fallback
			if ( 
				!is_array( $results ) || 
				!array_key_exists( 'Item', $results['Items'] ) ||
				array_key_exists( 'Errors', $results['Items']['Request'] )
			) {
				$fallback = true;
			}
		} else {
			$fallback = true;
		}
		
		// Return the output
		return $this->output( $fallback, $results );
	}

	/**
	 * output function.
	 * 
	 * @access public
	 * @param bool $fallback (default: false)
	 * @param array $results (default: array())
	 * @return void
	 */
	function output( $fallback = false, $results = array() ) {
		echo '<center>';
		if ( !$fallback ) {
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