<?php
/**
 * Class LWTV_Tests_Debugger
 *
 * Functionality tests for Debugger
 *
 * @package Lwtv_Plugin
 */

namespace LWTV\Tests;

use LWTV\_Components\Debugger;
use LWTV\Plugin;

/**
 * Debugger Tests.
 */
class Debugger_Test extends \WP_UnitTestCase {

	/**
	 * Test that the sanitizer fixes bad Twitter/X names
	 */
	public function test_sanitize_twitter() {
		$format = 'twitter';
		$real   = 'thisisaname';
		$bad    = 'this$is$a%name';
		$long   = 'https://twitter.com/' . $real;

		$sanitize_long = ( new Debugger() )->sanitize_social( $long, $format );
		$sanitize_real = ( new Debugger() )->sanitize_social( $real, $format );
		$sanitize_bad  = ( new Debugger() )->sanitize_social( $bad, $format );

		$this->assertSame( $sanitize_real, $sanitize_bad );
		$this->assertSame( $sanitize_real, $sanitize_long );
		$this->assertNotSame( $bad, $sanitize_bad );
	}

	/**
	 * Test that the sanitizer fixes bad Mastodon names
	 */
	public function test_sanitize_mastodon() {
		$format   = 'mastodon';
		$real     = 'thisisaname';
		$bad      = 'this$is$a%name';
		$long     = 'https://mstdn.social/@' . $real;
		$bad_long = 'https://mstdn.social/@' . $bad;

		$sanitize_long     = ( new Debugger() )->sanitize_social( $long, $format );
		$sanitize_bad_long = ( new Debugger() )->sanitize_social( $bad_long, $format );
		$sanitize_real     = ( new Debugger() )->sanitize_social( $real, $format );
		$sanitize_bad      = ( new Debugger() )->sanitize_social( $bad, $format );

		$this->assertSame( $sanitize_real, $sanitize_bad );
		$this->assertSame( $long, $sanitize_long );
		$this->assertNotSame( $bad_long, $sanitize_bad_long );
	}

	/**
	 * Test that the sanitizer fixes bad Instagram names
	 */
	public function test_sanitize_instagram() {
		$format = 'instagram';
		$real   = 'thisisaname';
		$bad    = 'this$is$a%name';
		$long   = 'https://instagram.com/' . $real;

		$sanitize_long = ( new Debugger() )->sanitize_social( $long, $format );
		$sanitize_real = ( new Debugger() )->sanitize_social( $real, $format );
		$sanitize_bad  = ( new Debugger() )->sanitize_social( $bad, $format );

		$this->assertSame( $sanitize_real, $sanitize_bad );
		$this->assertSame( $sanitize_real, $sanitize_long );
		$this->assertNotSame( $bad, $sanitize_bad );
	}

	/**
	 * Confirm IMDB Validation works.
	 */
	public function test_validate_imdb() {
		$real_actor = 'nm123456';
		$real_show  = 'tt123456';
		$bad_actor  = '1234b7kldf';
		$bad_show   = '1fsdf7kldf';

		$validate_actor = ( new Debugger() )->validate_imdb( $real_actor, 'actor' );
		$validate_show  = ( new Debugger() )->validate_imdb( $real_show, 'show' );

		$validate_bad_actor = ( new Debugger() )->validate_imdb( $bad_actor, 'actor' );
		$validate_bad_show  = ( new Debugger() )->validate_imdb( $bad_show, 'show' );

		$this->assertTrue( $validate_actor );
		$this->assertTrue( $validate_show );

		$this->assertFalse( $validate_bad_actor );
		$this->assertFalse( $validate_bad_show );
	}
}
