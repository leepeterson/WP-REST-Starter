<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Core\Field;

use Brain\Monkey;
use Inpsyde\WPRESTStarter\Common\Field\Collection;
use Inpsyde\WPRESTStarter\Common\Field\Field;
use Inpsyde\WPRESTStarter\Common\Field\Registry;
use Inpsyde\WPRESTStarter\Core\Field\Registry as Testee;
use Inpsyde\WPRESTStarter\Tests\Unit\TestCase;
use PHPUnit\Framework\Error\Notice;

/**
 * Test case for the field registry class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit\Core\Field
 * @since   1.0.0
 */
class RegistryTest extends TestCase {

	/**
	 * Tests failing silently when depended-upon function does not exist and not debugging.
	 *
	 * @since 1.0.0
	 *
	 * @runInSeparateProcess
	 *
	 * @return void
	 */
	public function test_register_fields_fails_silently() {

		define( 'WP_DEBUG', false );

		( new Testee() )->register_fields( \Mockery::mock( Collection::class ) );

		self::assertEquals( 0, did_action( Testee::ACTION_REGISTER ) );
	}

	/**
	 * Tests failing with a notice when depended-upon function does not exist and debugging.
	 *
	 * @since 1.0.0
	 *
	 * @runInSeparateProcess
	 *
	 * @return void
	 */
	public function test_register_fields_triggers_notice() {

		define( 'WP_DEBUG', true );

		self::expectException( Notice::class );

		( new Testee() )->register_fields( \Mockery::mock( Collection::class ) );

		self::assertEquals( 0, did_action( Testee::ACTION_REGISTER ) );
	}

	/**
	 * Tests registering fields of an empty field collection.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_register_fields_of_empty_collection() {

		$fields = \Mockery::mock( Collection::class );
		$fields->shouldReceive( 'getIterator' )
			->andReturn( new \ArrayIterator() );

		Monkey\WP\Actions::expectFired( Registry::ACTION_REGISTER )
			->once()
			->with( $fields );

		// This has to stay because the code checks for register_rest_field() being available.
		Monkey\Functions::expect( 'register_rest_field' )
			->never();

		( new Testee() )->register_fields( $fields );
	}

	/**
	 * Tests registering fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_register_fields() {

		$field_foo = \Mockery::mock( Field::class );
		$field_foo->shouldReceive( 'definition' )
			->andReturn( [ 'field_foo_definition' ] );

		$field_bar = \Mockery::mock( Field::class );
		$field_bar->shouldReceive( 'definition' )
			->andReturn( [ 'field_bar_definition' ] );

		$fields = \Mockery::mock( Collection::class );
		$fields->shouldReceive( 'getIterator' )
			->andReturn( new \ArrayIterator( [
				'resource_foo' => compact( 'field_foo' ),
				'resource_bar' => compact( 'field_foo', 'field_bar' ),
			] ) );

		Monkey\WP\Actions::expectFired( Registry::ACTION_REGISTER )
			->once()
			->with( $fields );

		Monkey\Functions::expect( 'register_rest_field' )
			->once()
			->with( 'resource_foo', 'field_foo', [ 'field_foo_definition' ] );
		Monkey\Functions::expect( 'register_rest_field' )
			->once()
			->with( 'resource_bar', 'field_foo', [ 'field_foo_definition' ] );
		Monkey\Functions::expect( 'register_rest_field' )
			->once()
			->with( 'resource_bar', 'field_bar', [ 'field_bar_definition' ] );

		( new Testee() )->register_fields( $fields );
	}
}
