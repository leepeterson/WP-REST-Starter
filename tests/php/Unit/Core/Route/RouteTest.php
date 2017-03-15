<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Core\Route;

use Inpsyde\WPRESTStarter\Core\Route\Route as Testee;
use Inpsyde\WPRESTStarter\Tests\TestCase;
use Mockery;

/**
 * Test case for the route class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit\Core\Route
 * @since   1.0.0
 */
class RouteTest extends TestCase {

	/**
	 * Tests getting the route definition.
	 *
	 * @since  1.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Route\Route::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Route\Route::get_options()
	 *
	 * @return void
	 */
	public function test_get_options() {

		$options_array = [ 'some', 'values', 'here' ];

		$options = Mockery::mock( 'Inpsyde\WPRESTStarter\Common\Arguments' );
		$options->shouldReceive( 'to_array' )
			->andReturn( $options_array );

		$this->assertSame( $options_array, ( new Testee( '', $options ) )->get_options() );
	}

	/**
	 * Tests getting the route name.
	 *
	 * @since  1.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Route\Route::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Route\Route::get_url()
	 *
	 * @return void
	 */
	public function test_get_url() {

		$url = 'some-url-here';

		$options = Mockery::mock( 'Inpsyde\WPRESTStarter\Common\Arguments' );

		$this->assertSame( $url, ( new Testee( $url, $options) )->get_url() );
	}
}
