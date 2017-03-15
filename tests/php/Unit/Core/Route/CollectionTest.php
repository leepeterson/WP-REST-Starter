<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Core\Route;

use Inpsyde\WPRESTStarter\Core\Route\Collection as Testee;
use Inpsyde\WPRESTStarter\Tests\TestCase;
use Mockery;

/**
 * Test case for the route collection class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit\Core\Route
 * @since   1.0.0
 */
class CollectionTest extends TestCase {

	/**
	 * Tests the class instance is returned.
	 *
	 * Only test the "fluent" part of the method. The "functional" part is covered in the according integration test.
	 *
	 * @since  1.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Route\Collection::add()
	 *
	 * @return void
	 */
	public function test_add_returns_this() {

		$testee = new Testee();

		$this->assertSame( $testee, $testee->add( Mockery::mock( 'Inpsyde\WPRESTStarter\Common\Route\Route' ) ) );
	}

	/**
	 * Tests the class instance is returned.
	 *
	 * Only test the "fluent" part of the method. The "functional" part is covered in the according integration test.
	 *
	 * @since  1.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Route\Collection::delete()
	 *
	 * @return void
	 */
	public function test_delete_returns_this() {

		$testee = new Testee();

		$this->assertSame( $testee, $testee->delete( 0 ) );
	}

	/**
	 * Tests returning an empty routes array.
	 *
	 * @since  1.0.0
	 * @since  1.1.0 Use `Testee::getIterator()` instead of deprecated `Testee::to_array()`.
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Route\Collection::getIterator()
	 *
	 * @return void
	 */
	public function test_getting_empty_array() {

		$routes = iterator_to_array( ( new Testee() )->getIterator() );

		$this->assertSame( [], $routes );
	}
}
