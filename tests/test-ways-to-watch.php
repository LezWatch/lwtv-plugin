<?php
/**
 * Class LWTV_Tests_Ways_To_Watch
 *
 * Functionality tests for Ways to Watch
 *
 * @package Lwtv_Plugin
 */

/**
 * Ways to Watch Tests.
 */
class LWTV_Tests_Ways_To_Watch extends WP_UnitTestCase {

	/**
	 * Test Generate Links
	 */
	public function test_generate_links() {
		$check_url = array( 'http://www.cwtv.com/shows/the-100/' );
		$links     = ( new LWTV_Theme_Ways_To_Watch() )->generate_links( $check_url );

		$expected = array( '<a href="http://www.cwtv.com/shows/the-100/" target="_blank" class="btn btn-primary" rel="nofollow">The CW</a>' );

		$this->assertNotEmpty( $links );
		$this->assertSame( $expected, $links );
	}

	/**
	 * Test Building a link.
	 */
	public function test_build_link() {
		$check_url  = 'http://www.cwtv.com/shows/the-100/';
		$check_name = 'The CW';
		$expected   = '<a href="' . $check_url . '" target="_blank" class="btn btn-primary" rel="nofollow">' . $check_name . '</a>';
		$build_link = ( new LWTV_Theme_Ways_To_Watch() )->build_link( $check_url, $check_name );

		$this->assertSame( $expected, $build_link );
	}

	/**
	 * Test Clean Sub Domain
	 *
	 * @return void
	 */
	public function test_clean_subdomain() {
		$check_url    = 'www.cwtv.com';
		$clean_url    = ( new LWTV_Theme_Ways_To_Watch() )->clean_subdomain( $check_url );
		$expected_url = 'cwtv.com';

		$this->assertSame( $expected_url, $clean_url );
	}

	/**
	 * Test Clean Sub Domain
	 *
	 * @return void
	 */
	public function test_clean_tld() {
		$check_url    = 'cwtv.com';
		$clean_url    = ( new LWTV_Theme_Ways_To_Watch() )->clean_tlds( $check_url );
		$expected_url = 'cwtv';

		$this->assertSame( $expected_url, $clean_url );
	}
}
