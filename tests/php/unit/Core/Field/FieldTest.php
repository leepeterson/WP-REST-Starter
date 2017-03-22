<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Core\Field;

use Inpsyde\WPRESTStarter\Core\Field\Field as Testee;
use Inpsyde\WPRESTStarter\Tests\Unit\TestCase;

/**
 * Test case for the field class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit\Core\Field
 * @since   1.0.0
 * @since   1.1.0 Added test methods for new setters, and adapt test methods for `Testee::get_definition()`.
 */
class FieldTest extends TestCase {

	/**
	 * Tests the class instance is returned.
	 *
	 * Only test the "fluent" part of the method. The "functional" part is covered in the according integration test.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_set_get_callback_returns_this() {

		$testee = new Testee( '' );

		self::assertSame( $testee, $testee->set_get_callback() );
	}

	/**
	 * Tests the class instance is returned.
	 *
	 * Only test the "fluent" part of the method. The "functional" part is covered in the according integration test.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_set_schema_returns_this() {

		$testee = new Testee( '' );

		self::assertSame( $testee, $testee->set_schema() );
	}

	/**
	 * Tests the class instance is returned.
	 *
	 * Only test the "fluent" part of the method. The "functional" part is covered in the according integration test.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_set_update_callback_returns_this() {

		$testee = new Testee( '' );

		self::assertSame( $testee, $testee->set_update_callback() );
	}

	/**
	 * Tests getting the default field definition.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_get_default_definition() {

		self::assertSame( [], ( new Testee( '' ) )->get_definition() );
	}

	/**
	 * Tests getting the field definition.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_get_passed_definition() {

		$definition = [ 'some', 'values', 'here' ];

		self::assertSame( $definition, ( new Testee( '', $definition ) )->get_definition() );
	}

	/**
	 * Tests getting the field name.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_name() {

		$name = 'some_name_here';

		self::assertSame( $name, ( new Testee( $name ) )->get_name() );
	}
}
