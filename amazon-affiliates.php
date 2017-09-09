<?php
/**
 * Name: Amazon Affiliate Code
 * Description: Auto Adds in Amazon Affiliate Code
 */

if ( ! defined('WPINC' ) ) die;

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
		
		$keywords = array();
		
		if ( is_singular( 'post_type_shows' ) || is_singular( 'post_type_characters' ) ) {
			// Add Title for shows and characters
			$keywords = get_the_title();			
		} else {
			// Annoying default else...
			$keywords = 'lgbt women television';
		}
		
		if ( empty( $keywords ) ) return;
			
		$conf = new GenericConfiguration();
		$client = new \GuzzleHttp\Client();
		$request = new \ApaiIO\Request\GuzzleRequest($client);
		
		$conf
		    ->setCountry('com')
		    ->setAccessKey( AMAZON_PRODUCT_API_KEY )
		    ->setSecretKey( AMAZON_PRODUCT_API_SECRET )
		    ->setAssociateTag('lezpress-20')
		    ->setRequest($request)
		    ->setResponseTransformer(new \ApaiIO\ResponseTransformer\XmlToArray());
		$apaiIO = new ApaiIO($conf);
	
		$search = new Search();
		$search->setCategory('DVD');
		$search->setBrowseNode('163450');
		$search->setKeywords( $keywords );

		$formattedResponse = $apaiIO->runOperation($search);		
		$top5items = array_slice( $formattedResponse['Items']['Item'], 0, 2 );
		
		echo '<center>';
		foreach ( $top5items as $item ) {
			?><iframe style="width:120px;height:240px;" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" src="//ws-na.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&OneJS=1&Operation=GetAdHtml&MarketPlace=US&source=ac&ref=tf_til&ad_type=product_link&tracking_id=lezpress-20&marketplace=amazon&region=US&placement=<?php echo $item['ASIN']; ?>&asins=<?php echo $item['ASIN']; ?>&show_border=true&link_opens_in_new_window=true&price_color=333333&title_color=0066C0&bg_color=FFFFFF">
			</iframe>&nbsp;<?php
		}
		echo '<p><a href="' . $formattedResponse['Items']['MoreSearchResultsUrl'] . '" target="_blank">More Results ... </a></p>';
		echo '</center>';
	}
	
}

new LWTV_Amazon();