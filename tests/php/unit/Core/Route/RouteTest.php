<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Core\Route;

use Inpsyde\WPRESTStarter\Common\Arguments;
use Inpsyde\WPRESTStarter\Core\Route\Route as Testee;
use Inpsyde\WPRESTStarter\Tests\Unit\TestCase;

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
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_options() {

		$options_array = [ 'some', 'values', 'here' ];

		$options = \Mockery::mock( Arguments::class );
		$options->shouldReceive( 'to_array' )
			->andReturn( $options_array );

		self::assertSame( $options_array, ( new Testee( '', $options ) )->options() );
	}

	/**
	 * Tests getting the route name.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_url() {

		$url = 'some-url-here';

		$options = \Mockery::mock( Arguments::class );

		self::assertSame( $url, ( new Testee( $url, $options) )->url() );
	}
}
