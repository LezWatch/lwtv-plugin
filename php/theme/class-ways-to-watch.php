<?php
/**
 * Name: Ways to Watch
 * Description: Edit 'ways to watch' on the fly, based on networks and links
 *
 */

namespace LWTV\Theme;

class Ways_To_Watch {

	const SUBDOMAINS = array( 'gshow.', 'play.', 'premium.', 'watch.', 'www.' );
	const TLDS       = array( '.com', '.co.nz', '.co.uk', '.ca', '.cbc', '.co', '.fandom', '.globo', '.go', '.org', '.tv' );

	// URLs that belong to someone else.
	const URL_OWNER = array(
		'7eer'                       => 'cbs',
		'southpark.cc'               => 'cc',
		'itunes'                     => 'apple',
		'tv.apple'                   => 'apple',
		'itunes.apple'               => 'apple',
		'watch.amazon'               => 'amazon',
		'disneynow'                  => 'disney',
		'disneyplus'                 => 'disney',
		'disneyplusoriginals.disney' => 'disney',
		'globoplay.globo'            => 'globo',
		'gshow.globo'                => 'globo',
		'lobo'                       => 'globo',
		'lesflicksvod.vhx.tv'        => 'lesflicks',
		'oprah'                      => 'own',
		'paus.tv'                    => 'paus',
		'watch.paus'                 => 'paus',
		'peacocktv'                  => 'peacock',
		'paramountplus'              => 'cbs',
		'primevideo'                 => 'amazon',
		'sho'                        => 'showtime',
		'showtimeanytime'            => 'showtime',
		'youtu.be'                   => 'youtube',
	);

	// URL and name params based on host.
	const PRETTY_NAME = array(
		'acorn.tv'            => 'Acorn',
		'adultswim'           => 'Adult Swim',
		'amazon'              => 'Prime Video',
		'atresplayer'         => 'ATRESPlayer',
		'apple'               => 'Apple TV+',
		'bbcamerica'          => 'BBC America',
		'bet.plus'            => 'BET+',
		'bifltheseries'       => 'BIFL',
		'cartoonnetwork'      => 'Cartoon Network',
		'cc'                  => 'Comedy Central',
		'cwtv'                => 'The CW',
		'dcuniverse'          => 'DC Universe',
		'disney'              => 'Disney+',
		'globaltv'            => 'GlobalTV',
		'hallmarkchannel'     => 'Hallmark Channel',
		'hbomax'              => 'HBO Max',
		'lesflicksvod'        => 'LesFlicks',
		'paus'                => 'paus',
		'peacock'             => 'Peacock TV (NBC)',
		'peepoodo.bobbypills' => 'BobbyPills',
		'reelwomensnetwork'   => 'Reel Women\'s Network',
		'roosterteeth'        => 'Roster Teeth',
		'svtvnetwork'         => 'SVtv',
		'tellofilms'          => 'Tello Films',
		'tntdrama'            => 'TNT Drama',
		'youtube'             => 'YouTube',
		'tvnz'                => 'TVNZ',
		'tv.line.me'          => 'LineTV',
		'tv.youtube'          => 'YouTube TV',
	);

	/**
	 * Call Custom Affiliate Links
	 *
	 * This is used by shows to figure out where people can watch things
	 * There's some juggling for certain sites
	 */
	public function ways_to_watch( $id ) {

		$affiliate_urls = get_post_meta( $id, 'lezshows_affiliate', true );
		$links          = self::generate_links( $affiliate_urls );
		$link_output    = implode( '', $links );

		$icon   = lwtv_plugin()->get_symbolicon( 'tv-hd.svg', 'fa-tv' );
		$output = $icon . '<span class="how-to-watch">Ways to Watch:</span> ' . $link_output;

		return $output;
	}

	/**
	 * Generate Affiliate URLs
	 *
	 * @param  array $affiliate_urls
	 * @return array
	 */
	public function generate_links( $affiliate_urls ) {
		$links = array();

		// Parse each URL to figure out who it is...
		foreach ( $affiliate_urls as $url ) {
			$parsed_url = wp_parse_url( $url );
			$hostname   = $parsed_url['host'];

			// Clean the subdomain.
			$hostname = $this->clean_subdomain( $hostname );

			// Remove TLDs from the end:
			$hostname = $this->clean_tlds( $hostname );

			// Get the slug based on the hostname to array translation.
			$slug = ( array_key_exists( $hostname, self::URL_OWNER ) ) ? self::URL_OWNER[ $hostname ] : $hostname;

			// Clean the name
			$name = $this->clean_name( $slug );

			// Add to the links array.
			$links[] = $this->build_link( $url, $name );
		}

		return $links;
	}

	/**
	 * Build formatted link
	 *
	 * @param  string $url
	 * @param  string $name
	 * @param  string $extra
	 * @return string
	 */
	public function build_link( $url, $name ): string {
		return '<a href="' . $url . '" target="_blank" class="btn btn-primary" rel="nofollow">' . $name . '</a>';
	}

	/**
	 * Clean Subdomains
	 *
	 * @param  string $hostname
	 * @return string
	 */
	public function clean_subdomain( $hostname ): string {
		foreach ( self::SUBDOMAINS as $remove ) {
			$count = strlen( $remove );
			if ( substr( $hostname, 0, $count ) === $remove ) {
				$hostname = ltrim( $hostname, $remove );
				break;
			}
		}

		return $hostname;
	}

	/**
	 * Clean TLDs off hosts
	 *
	 * @param  string $hostname
	 * @return string
	 */
	public function clean_tlds( $hostname ): string {
		foreach ( self::TLDS as $remove ) {
			$count = strlen( $remove );
			if ( substr( $hostname, -$count ) === $remove ) {
				$hostname = substr( $hostname, 0, -$count );
				break;
			}
		}

		return $hostname;
	}

	/**
	 * Clean the pretty name based on the slug
	 *
	 * @param  string $slug
	 * @return string
	 */
	private function clean_name( string $slug ): string {
		// Set name based on slug in url_array. If not set, capitalize string.
		$name = ( isset( self::PRETTY_NAME[ $slug ] ) ) ? self::PRETTY_NAME[ $slug ] : ucfirst( $slug );

		// If it's three letters, it's always capitalized.
		$name = ( ! isset( self::PRETTY_NAME[ $slug ] ) && 3 === strlen( $name ) ) ? strtoupper( $name ) : $name;

		// Crazy failsafe:
		if ( empty( $name ) ) {
			$name = 'Watch Online';
		}

		return $name;
	}
}
