<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Integration\Factory;

use Brain\Monkey;
use Inpsyde\WPRESTStarter\Factory\PermissionCallback as Testee;
use Inpsyde\WPRESTStarter\Tests\Integration\TestCase;

/**
 * Test case for the permission callback factory class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Integration\Factory
 * @since   2.0.0
 */
class PermissionCallbackTest extends TestCase {

	/**
	 * Tests the current_user_can permission callback with no capabilities.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_current_user_can_with_no_capabilities() {

		$callback = ( new Testee() )->current_user_can();

		self::assertSame( true, $callback() );
	}

	/**
	 * Tests the current_user_can permission callback with the single capability.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_current_user_can_with_single_capability() {

		$callback = ( new Testee() )->current_user_can( 'some_cap_here' );

		Monkey\Functions::when( 'current_user_can' )
			->justReturn( true );

		self::assertSame( true, $callback() );
	}

	/**
	 * Tests the current_user_can permission callback without the single capability.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_current_user_can_without_single_capability() {

		$callback = ( new Testee() )->current_user_can( 'some_cap_here' );

		Monkey\Functions::when( 'current_user_can' )
			->justReturn( false );

		self::assertSame( false, $callback() );
	}

	/**
	 * Tests the current_user_can permission callback with multiple capabilities.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_current_user_can_with_multiple_capabilities() {

		$capabilities = [
			'some_cap_here',
			'some_other_cap',
			'yet_another_cap',
		];

		$callback = ( new Testee() )->current_user_can( ...$capabilities );

		Monkey\Functions::when( 'current_user_can' )
			->justReturn( true );

		self::assertSame( true, $callback() );
	}

	/**
	 * Tests the current_user_can permission callback with multiple capabilities of which the first fails.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_current_user_can_with_all_but_the_first_of_multiple_capabilities() {

		$capabilities = [
			'some_cap_here',
			'some_other_cap',
			'yet_another_cap',
		];

		$callback = ( new Testee() )->current_user_can( ...$capabilities );

		$failing_capability = reset( $capabilities );

		Monkey\Functions::expect( 'current_user_can' )
			->andReturnUsing( function ( $capability ) use ( $failing_capability ) {

				return $capability !== $failing_capability;
			} );

		self::assertSame( false, $callback() );
	}

	/**
	 * Tests the current_user_can permission callback with multiple capabilities of which the last fails.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_current_user_can_with_all_but_the_last_of_multiple_capabilities() {

		$capabilities = [
			'some_cap_here',
			'some_other_cap',
			'yet_another_cap',
		];

		$callback = ( new Testee() )->current_user_can( ...$capabilities );

		$failing_capability = end( $capabilities );

		Monkey\Functions::expect( 'current_user_can' )
			->andReturnUsing( function ( $capability ) use ( $failing_capability ) {

				return $capability !== $failing_capability;
			} );

		self::assertSame( false, $callback() );
	}

	/**
	 * Tests the current_user_can permission callback with none of multiple capabilities.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_current_user_can_with_none_of_multiple_capabilities() {

		$capabilities = [
			'some_cap_here',
			'some_other_cap',
			'yet_another_cap',
		];

		$callback = ( new Testee() )->current_user_can( ...$capabilities );

		Monkey\Functions::when( 'current_user_can' )
			->justReturn( false );

		self::assertSame( false, $callback() );
	}

	/**
	 * Tests the current_user_can_for_site permission callback.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function test_current_user_can_for_site() {

		$site_id = 42;

		$capabilities = [
			'some_cap_here',
			'some_other_cap',
			'yet_another_cap',
		];

		$current_user_can = (bool) mt_rand( 0, 1 );

		Monkey\Functions::when( 'is_multisite' )
			->justReturn( true );

		$testee = \Mockery::mock( Testee::class . '[current_user_can]' );
		$testee->shouldReceive( 'current_user_can' )
			->with( ...$capabilities )
			->andReturn( function () use ( $current_user_can ) {

				return $current_user_can;
			} );

		$callback = $testee->current_user_can_for_site( $site_id, ...$capabilities );

		Monkey\Functions::expect( 'switch_to_blog' )
			->with( $site_id );

		Monkey\Functions::expect( 'restore_current_blog' );

		self::assertSame( $current_user_can, $callback() );
	}

	/**
	 * Tests the current_user_can_for_site permission callback on a WordPress single-site installation.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function test_current_user_can_for_site_on_single_site() {

		$site_id = 42;

		$capabilities = [
			'some_cap_here',
			'some_other_cap',
			'yet_another_cap',
		];

		$current_user_can = (bool) mt_rand( 0, 1 );

		Monkey\Functions::when( 'is_multisite' )
			->justReturn( false );

		$testee = \Mockery::mock( Testee::class . '[current_user_can]' );
		$testee->shouldReceive( 'current_user_can' )
			->with( ...$capabilities )
			->andReturn( function () use ( $current_user_can ) {

				return $current_user_can;
			} );

		$callback = $testee->current_user_can_for_site( $site_id, ...$capabilities );

		self::assertSame( $current_user_can, $callback() );
	}
}
