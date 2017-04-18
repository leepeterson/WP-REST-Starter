<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Integration\Core\Request;

use Brain\Monkey;
use Inpsyde\WPRESTStarter\Common\Field\Access;
use Inpsyde\WPRESTStarter\Core\Request\FieldProcessor as Testee;
use Inpsyde\WPRESTStarter\Tests\Integration\TestCase;

/**
 * Test case for the field processor class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Integration\Core\Request
 * @since   3.0.0
 */
class FieldProcessorTest extends TestCase {

	/**
	 * Tests updating fields of the given object correctly handles a WP_Error object returned by an update callback.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function test_update_fields_correctly_handles_error_object() {

		$object = [
			'some',
			'data',
			'here',
		];

		$wp_error = \Mockery::mock( \WP_Error::class );

		$request = \Mockery::mock( \WP_REST_Request::class, \ArrayAccess::class );
		$request->shouldReceive( 'offsetExists' )
			->andReturn( true );
		$request->shouldReceive( 'offsetGet' );

		$object_type = 'some_type_here';

		$field_access = \Mockery::mock( Access::class );
		$field_access->shouldReceive( 'get_fields' )
			->with( $object_type )
			->andReturn( [
				[
					'update_callback' => function () use ( $wp_error ) {

						return $wp_error;
					},
				],
			] );

		Monkey\Functions::when( 'is_wp_error' )
			->justReturn( true );

		$testee = new Testee( $field_access );

		$testee->update_fields_for_object( $object, $request, $object_type );

		self::assertSame( $wp_error, $testee->get_last_error() );
	}

	/**
	 * Tests updating fields of the given object correctly unsets an existing last error object.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function test_update_fields_unsets_last_error_object() {

		$object = [
			'some',
			'data',
			'here',
		];

		$wp_error = \Mockery::mock( \WP_Error::class );

		$request = \Mockery::mock( \WP_REST_Request::class, \ArrayAccess::class );
		$request->shouldReceive( 'offsetExists' )
			->andReturn( true );
		$request->shouldReceive( 'offsetGet' );

		$object_type = 'some_type_here';

		$field_access = \Mockery::mock( Access::class );
		$field_access->shouldReceive( 'get_fields' )
			->with( $object_type )
			->andReturnValues( [
				[
					[
						'update_callback' => function () use ( $wp_error ) {

							return $wp_error;
						},
					],
				],
				[
					[
						'update_callback' => 'noop',
					],
				],
			] );

		Monkey\Functions::expect( 'is_wp_error' )
			->andReturnValues( [
				true,
				false,
			] );

		$testee = new Testee( $field_access );

		$testee->update_fields_for_object( $object, $request, $object_type );

		self::assertSame( $wp_error, $testee->get_last_error() );

		$testee->update_fields_for_object( $object, $request, $object_type );

		self::assertSame( null, $testee->get_last_error() );
	}
}
