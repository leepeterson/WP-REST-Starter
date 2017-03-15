<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Integration\Core\Field;

use Inpsyde\WPRESTStarter\Core\Field\Field as Testee;
use Inpsyde\WPRESTStarter\Tests\TestCase;
use Mockery;

/**
 * Test case for the field class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Integration\Core\Field
 * @since   1.1.0
 */
class FieldTest extends TestCase {

	/**
	 * Tests setting the GET callback.
	 *
	 * @since  1.1.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::get_definition()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::set_get_callback()
	 *
	 * @return void
	 */
	public function test_set_get_callback() {

		$reader = Mockery::mock( 'Inpsyde\WPRESTStarter\Common\Field\Reader' );

		$testee = ( new Testee( '' ) )->set_get_callback( $reader );

		$expected = [
			'get_callback' => [ $reader, 'get_value' ],
		];

		$this->assertEquals( $expected, $testee->get_definition() );
	}

	/**
	 * Tests resetting the GET callback.
	 *
	 * @since  1.1.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::get_definition()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::set_get_callback()
	 *
	 * @return void
	 */
	public function test_reset_get_callback() {

		$testee = ( new Testee( '', [ 'get_callback' => 'get_callback' ] ) )->set_get_callback();

		$expected = [
			'get_callback' => null,
		];

		$this->assertEquals( $expected, $testee->get_definition() );
	}

	/**
	 * Tests setting the schema callback.
	 *
	 * @since  1.1.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::get_definition()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::set_schema()
	 *
	 * @return void
	 */
	public function test_set_schema() {

		$schema = Mockery::mock( 'Inpsyde\WPRESTStarter\Common\Schema' );

		$testee = ( new Testee( '' ) )->set_schema( $schema );

		$expected = [
			'schema' => [ $schema, 'get_schema' ],
		];

		$this->assertEquals( $expected, $testee->get_definition() );
	}

	/**
	 * Tests resetting the schema callback.
	 *
	 * @since  1.1.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::get_definition()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::set_schema()
	 *
	 * @return void
	 */
	public function test_reset_schema() {

		$testee = ( new Testee( '', [ 'schema' => 'schema' ] ) )->set_schema();

		$expected = [
			'schema' => null,
		];

		$this->assertEquals( $expected, $testee->get_definition() );
	}

	/**
	 * Tests setting the UPDATE callback.
	 *
	 * @since  1.1.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::get_definition()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::set_update_callback()
	 *
	 * @return void
	 */
	public function test_set_update_callback() {

		$updater = Mockery::mock( 'Inpsyde\WPRESTStarter\Common\Field\Updater' );

		$testee = ( new Testee( '' ) )->set_update_callback( $updater );

		$expected = [
			'update_callback' => [ $updater, 'update_value' ],
		];

		$this->assertEquals( $expected, $testee->get_definition() );
	}

	/**
	 * Tests resetting the UPDATE callback.
	 *
	 * @since  1.1.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::get_definition()
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Field::set_update_callback()
	 *
	 * @return void
	 */
	public function test_reset_update_callback() {

		$testee = ( new Testee( '', [ 'update_callback' => 'update_callback' ] ) )->set_update_callback();

		$expected = [
			'update_callback' => null,
		];

		$this->assertEquals( $expected, $testee->get_definition() );
	}
}
