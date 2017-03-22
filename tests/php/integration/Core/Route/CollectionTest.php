<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Integration\Core\Route;

use Inpsyde\WPRESTStarter\Core\Route\Collection as Testee;
use Inpsyde\WPRESTStarter\Core\Route\Options;
use Inpsyde\WPRESTStarter\Core\Route\Route;
use Inpsyde\WPRESTStarter\Tests\Integration\TestCase;

/**
 * Test case for the route collection class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Integration\Core\Route
 * @since   1.0.0
 */
class CollectionTest extends TestCase {

	/**
	 * Tests adding and deleting routes.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Use `Testee::getIterator()` instead of deprecated `Testee::to_array()`.
	 *
	 * @return void
	 */
	public function test_add_and_delete() {

		$testee = new Testee();

		$url = 'some-url-here';

		$route = new Route( $url, new Options() );

		$testee->add( $route );

		$routes = iterator_to_array( $testee->getIterator() );

		self::assertSame( $route, $routes[0] );

		$testee->delete( 0 );

		$routes = iterator_to_array( $testee->getIterator() );

		self::assertTrue( empty( $routes ) );
	}
}
