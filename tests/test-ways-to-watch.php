<?php
/**
 * Class LWTV_Tests_Ways_To_Watch
 *
 * @package Lwtv_Plugin
 */

/**
 * Ways to Watch Tests.
 */
class LWTV_Tests_Ways_To_Watch extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	public function test_CW() {
		$check_cw = array( 'http://www.cwtv.com/shows/the-100/' );
		$links_cw = ( new LWTV_Ways_To_Watch_Output() )->generate_links( $check_cw );

		$output = array( '<a href="http://www.cwtv.com/shows/the-100/" target="_blank" class="btn btn-primary" rel="nofollow">The CW</a>' );

		$this->assertNotEmpty( $links_cw );
		$this->assertSame( $output, $links_cw );
	}
}
