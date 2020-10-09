<?php
/**
 * Name: Ways to Watch
 * Description: Edit 'ways to watch' on the fly, based on networks and links
 *
 * Links:
 * https://appleservices-console.partnerize.com/v2/overview/overview
 * https://affiliate.itunes.apple.com/resources/documentation/basic_affiliate_link_guidelines_for_the_phg_network/
 */


class LWTV_Ways_To_Watch {
	/**
	 * Call Custom Affiliate Links
	 * This is used by shows to figure out where people can watch things
	 * There's some juggling for certain sites
	 */
	public function affiliate_link( $id ) {

		$affiliate_url = get_post_meta( $id, 'lezshows_affiliate', true );
		$links         = array();

		// Parse each URL to figure out who it is...
		foreach ( $affiliate_url as $url ) {
			$parsed_url = wp_parse_url( $url );
			$hostname   = $parsed_url['host'];
			$clean_url  = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $parsed_url['path'];

			// Clean the URL to get the top domain ...
			$removal_array = array( 'www.', '.com', 'itunes.', '.co.uk', '.ca', '.go', '.org' );
			foreach ( $removal_array as $removal ) {
				$hostname = str_replace( $removal, '', $hostname );
			}

			// URLs that belong to someone else.
			$host_array = array(
				'7eer'            => 'cbs',
				'itunes'          => 'apple',
				'tv.apple'        => 'apple',
				'watch.amazon'    => 'amazon',
				'peacocktv'       => 'peacock',
				'sho'             => 'showtime',
				'showtimeanytime' => 'showtime',
				'youtu.be'        => 'youtube',
			);

			// URL and name params based on host.
			$url_array = array(
				'amazon'         => array(
					'url'   => $clean_url . 'ref=as_li_tl?ie=UTF8&tag=lezpress-20',
					'extra' => '<img src="//ir-na.amazon-adsystem.com/e/ir?t=lezpress-20&l=pf4&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />',
					'name'  => 'Amazon Prime TV',
				),
				'apple'          => array(
					'url'  => $clean_url . '?at=1010lMaT&ct=lwtv',
					'name' => 'Apple TV+',
				),
				'bbcamerica'     => array(
					'name' => 'BBC America',
				),
				'cartoonnetwork' => array(
					'name' => 'Cartoon Network',
				),
				'cbs'            => array(
					'url'   => 'https://cbsallaccess.qflm.net/c/1242493/176097/3065',
					'extra' => '<img height="0" width="0" src="//cbsallaccess.qflm.net/i/1242493/176097/3065" style="position:absolute;visibility:hidden;" border="0" />',
					'name'  => 'CBS All Access',
				),
				'cwtv'           => array(
					'name' => 'The CW',
				),
				'dcuniverse'     => array(
					'name' => 'DC Universe',
				),
				'disneyplus'     => array(
					'name' => 'Disney+',
				),
				'hbomax'         => array(
					'name' => 'HBO Max',
				),
				'peacock'        => array(
					'name' => 'Peacock TV (NBC)',
				),
				'roosterteeth'   => array(
					'name' => 'Roster Teeth',
				),
				'tellofilms'     => array(
					'name' => 'Tello Films',
				),
				'youtube'        => array(
					'name' => 'YouTube',
				),
				'tv.youtube'     => array(
					'name' => 'YouTube.TV',
				),
			);

			// Get the slug based on the hostname to host_array.
			$slug = ( array_key_exists( $hostname, $host_array ) ) ? $host_array[ $hostname ] : $hostname;

			// Set extra based on slug in url_array.
			// If not set, leave empty.
			$extra = ( isset( $url_array[ $slug ]['extra'] ) ) ? $url_array[ $slug ]['extra'] : '';

			// Set name based on slug in url_array.
			// If not set, capitalize string.
			// If it's three letters, it's always capitalized.
			$name = ( isset( $url_array[ $slug ]['name'] ) ) ? $url_array[ $slug ]['name'] : ucfirst( $slug );
			$name = ( ! isset( $url_array[ $slug ]['name'] ) && 3 === strlen( $name ) ) ? strtoupper( $name ) : $name;

			// Set URL based on slug in url_array
			// If not set, use $clean_url
			$url = ( isset( $url_array[ $slug ]['url'] ) ) ? $url_array[ $slug ]['url'] : $clean_url;

			// Add to the links array.
			$links[] = '<a href="' . $url . '" target="_blank" class="btn btn-primary" rel="nofollow">' . $name . '</a>' . $extra;
		}

		$link_output = implode( '', $links );

		$icon   = ( new LWTV_Functions() )->symbolicons( 'tv-hd.svg', 'fa-tv' );
		$output = $icon . '<span class="how-to-watch">Ways to Watch:</span> ' . $link_output;

		return $output;
	}
}
