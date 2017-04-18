<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Core\Route;

use Brain\Monkey;
use Inpsyde\WPRESTStarter\Common\Route\Collection;
use Inpsyde\WPRESTStarter\Common\Route\Registry;
use Inpsyde\WPRESTStarter\Common\Route\Route;
use Inpsyde\WPRESTStarter\Core\Route\Registry as Testee;
use Inpsyde\WPRESTStarter\Tests\Unit\TestCase;

/**
 * Test case for the route registry class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit\Core\Route
 * @since   1.0.0
 */
class RegistryTest extends TestCase {

	/**
	 * Tests registering routes of an empty route collection.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_register_routes_of_empty_collection() {

		$namespace = 'some-namespace-here';

		$routes = \Mockery::mock( Collection::class );
		$routes->shouldReceive( 'getIterator' )
			->andReturn( new \ArrayIterator() );

		Monkey\WP\Actions::expectFired( Registry::ACTION_REGISTER )
			->once()
			->with( $routes, $namespace );

		( new Testee( $namespace ) )->register_routes( $routes );
	}

	/**
	 * Tests registering routes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_register_routes() {

		$namespace = 'some-namespace-here';

		$route_foo = \Mockery::mock( Route::class );
		$route_foo->shouldReceive( 'url' )
			->andReturn( 'route_foo_url' );
		$route_foo->shouldReceive( 'options' )
			->andReturn( [ 'route_foo_options' ] );

		$route_bar = \Mockery::mock( Route::class );
		$route_bar->shouldReceive( 'url' )
			->andReturn( 'route_bar_url' );
		$route_bar->shouldReceive( 'options' )
			->andReturn( [ 'route_bar_options' ] );

		$routes = \Mockery::mock( Collection::class );
		$routes->shouldReceive( 'getIterator' )
			->andReturn( new \ArrayIterator( [
				$route_foo,
				$route_bar,
				$route_foo,
				$route_foo,
				$route_bar,
			] ) );

		Monkey\WP\Actions::expectFired( Registry::ACTION_REGISTER )
			->once()
			->with( $routes, $namespace );

		Monkey\Functions::expect( 'register_rest_route' )
			->times( 3 )
			->with( $namespace, 'route_foo_url', [ 'route_foo_options' ] );
		Monkey\Functions::expect( 'register_rest_route' )
			->times( 2 )
			->with( $namespace, 'route_bar_url', [ 'route_bar_options' ] );

		( new Testee( $namespace ) )->register_routes( $routes );
	}
}
