<?php
/**
 * Class Symbolicons_Test
 *
 * Functionality tests for Symbolicons
 *
 * @package Lwtv_Plugin
 */

namespace LWTV\Tests;

use LWTV\_Components\Symbolicons;

/**
 * Ways to Watch Tests.
 */
class Symbolicons_Test extends \WP_UnitTestCase {

	/**
	 * Set up.
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		$this->instance  = new Symbolicons();
		$this->path      = '/tmp/wordpress/wp-content/uploads/lezpress-icons/symbolicons/';
		$this->test_path = '/tmp/wordpress-tests-lib/data/themedir1/default/images/';
		$this->beer_mug  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>beer-mug</title><g id="beer-mug"><path d="M19.5,9.5A5.39,5.39,0,0,1,18,9.28V18a1,1,0,0,1-2,0V9.64a5.74,5.74,0,0,1-4,0V18a1,1,0,0,1-2,0V9.28a5.39,5.39,0,0,1-1.5.22A5.47,5.47,0,0,1,6.22,9H4a3,3,0,0,0-3,3v5a3,3,0,0,0,3,3H6a4,4,0,0,0,4,4h8a4,4,0,0,0,4-4V9h-.22A5.47,5.47,0,0,1,19.5,9.5ZM6,18H4a1,1,0,0,1-1-1V12a1,1,0,0,1,1-1H6ZM8.5,7.5a3.5,3.5,0,0,0,2.42-1,3.94,3.94,0,0,0,6.16,0,3.5,3.5,0,1,0,0-5,3.94,3.94,0,0,0-6.16,0,3.5,3.5,0,1,0-2.42,6Z"/></g></svg>';
		$this->square    = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve"><g><rect fill="#231F20" width="28" height="28"/></g></svg>';

		// Make the Symbolicons path
		if ( ! is_dir( $this->path ) ) {
			mkdir( $this->path, 0777, true );
		}
		file_put_contents( $this->path . 'beer-mug.svg', $this->beer_mug );

		// Make the test folder path
		if ( ! is_dir( $this->test_path ) ) {
			mkdir( $this->test_path, 0777, true );
		}
		file_put_contents( $this->test_path . 'square.svg', $this->square );
	}

	/**
	 * Test a plain icon
	 *
	 * @return void
	 */
	public function test_get_icon_svg_plain() {

		$svg_icon = 'data:image/svg+xml;base64,' . base64_encode( '<svg width="100%" height="100%" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="fill:#82878c"><path d="M4,10c0,-4.411 3.589,-8 8,-8c4.411,0 8,3.589 8,8v2.08c0.706,0.102 1.378,0.308 2,0.605v-2.685c0,-5.514 -4.486,-10 -10,-10c-5.514,0 -10,4.486 -10,10v2.685c0.622,-0.297 1.294,-0.503 2,-0.605Zm8,-6c-3.309,0 -6,2.691 -6,6v1.025c0.578,-0.772 1.294,-1.43 2.112,-1.929c0.412,-1.77 1.994,-3.096 3.888,-3.096c1.894,0 3.476,1.326 3.888,3.096c0.819,0.499 1.534,1.157 2.112,1.929v-1.025c0,-3.309 -2.691,-6 -6,-6Zm7,10h-1.712c-0.654,-2.307 -2.771,-4 -5.288,-4c-2.517,0 -4.634,1.693 -5.288,4h-1.712c-2.761,0 -5,2.239 -5,5c0,2.761 2.239,5 5,5h14c2.761,0 5,-2.239 5,-5c0,-2.761 -2.239,-5 -5,-5Z" transform="scale(0.666667)" fill="#82878c"></path></svg>' );
		$get_icon = ( new Symbolicons() )->get_icon_svg();

		$this->assertSame( $get_icon, $svg_icon );
	}

	/**
	 * Test an icon with a color
	 *
	 * @return void
	 */
	public function test_get_icon_svg_fill() {

		$svg_icon = 'data:image/svg+xml;base64,' . base64_encode( '<svg width="100%" height="100%" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="fill:#36f559"><path d="M4,10c0,-4.411 3.589,-8 8,-8c4.411,0 8,3.589 8,8v2.08c0.706,0.102 1.378,0.308 2,0.605v-2.685c0,-5.514 -4.486,-10 -10,-10c-5.514,0 -10,4.486 -10,10v2.685c0.622,-0.297 1.294,-0.503 2,-0.605Zm8,-6c-3.309,0 -6,2.691 -6,6v1.025c0.578,-0.772 1.294,-1.43 2.112,-1.929c0.412,-1.77 1.994,-3.096 3.888,-3.096c1.894,0 3.476,1.326 3.888,3.096c0.819,0.499 1.534,1.157 2.112,1.929v-1.025c0,-3.309 -2.691,-6 -6,-6Zm7,10h-1.712c-0.654,-2.307 -2.771,-4 -5.288,-4c-2.517,0 -4.634,1.693 -5.288,4h-1.712c-2.761,0 -5,2.239 -5,5c0,2.761 2.239,5 5,5h14c2.761,0 5,-2.239 5,-5c0,-2.761 -2.239,-5 -5,-5Z" transform="scale(0.666667)" fill="#36f559"></path></svg>' );
		$get_icon = ( new Symbolicons() )->get_icon_svg( true, '#36f559' );

		$this->assertSame( $get_icon, $svg_icon );
	}

	/**
	 * Test the folder path is correct.
	 */
	public function test_get_icon_file() {

		$svg       = 'beer-mug.svg';
		$file_path = $this->path . $svg;
		$get_file  = ( new Symbolicons() )->get_icon_file( $svg );

		// Test that the file is properly called
		$this->assertSame( $file_path, $get_file );
	}

	/**
	 * Test that getting a symbolicon works.
	 */
	public function test_get_symbolicon() {
		$get_existing_symbolicon     = ( new Symbolicons() )->get_symbolicon( $svg );
		$get_non_existing_symbolicon = ( new Symbolicons() )->get_symbolicon( 'fake.svg' );
		$expected_symbolicon         = '<span class="symbolicon" role="img">' . $this->beer_mug . '</span>';

		$this->assertSame( $get_existing_symbolicon, $expected_symbolicon );
		$this->assertNotSame( $get_non_existing_symbolicon, $get_existing_symbolicon );
	}

}
