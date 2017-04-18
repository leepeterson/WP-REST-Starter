<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Integration\Core\Field;

use Inpsyde\WPRESTStarter\Common\Field\Reader;
use Inpsyde\WPRESTStarter\Common\Schema;
use Inpsyde\WPRESTStarter\Common\Field\Updater;
use Inpsyde\WPRESTStarter\Core\Field\Field as Testee;
use Inpsyde\WPRESTStarter\Tests\Integration\TestCase;

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
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_set_get_callback() {

		$reader = \Mockery::mock( Reader::class );

		$testee = ( new Testee( '' ) )->set_get_callback( $reader );

		$expected = [
			'get_callback' => [ $reader, 'get_value' ],
		];

		self::assertEquals( $expected, $testee->definition() );
	}

	/**
	 * Tests resetting the GET callback.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_reset_get_callback() {

		$testee = ( new Testee( '', [ 'get_callback' => 'get_callback' ] ) )->set_get_callback();

		$expected = [
			'get_callback' => null,
		];

		self::assertEquals( $expected, $testee->definition() );
	}

	/**
	 * Tests setting the schema callback.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_set_schema() {

		$schema = \Mockery::mock( Schema::class );

		$testee = ( new Testee( '' ) )->set_schema( $schema );

		$expected = [
			'schema' => [ $schema, 'definition' ],
		];

		self::assertEquals( $expected, $testee->definition() );
	}

	/**
	 * Tests resetting the schema callback.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_reset_schema() {

		$testee = ( new Testee( '', [ 'schema' => 'schema' ] ) )->set_schema();

		$expected = [
			'schema' => null,
		];

		self::assertEquals( $expected, $testee->definition() );
	}

	/**
	 * Tests setting the UPDATE callback.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_set_update_callback() {

		$updater = \Mockery::mock( Updater::class );

		$testee = ( new Testee( '' ) )->set_update_callback( $updater );

		$expected = [
			'update_callback' => [ $updater, 'update_value' ],
		];

		self::assertEquals( $expected, $testee->definition() );
	}

	/**
	 * Tests resetting the UPDATE callback.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_reset_update_callback() {

		$testee = ( new Testee( '', [ 'update_callback' => 'update_callback' ] ) )->set_update_callback();

		$expected = [
			'update_callback' => null,
		];

		self::assertEquals( $expected, $testee->definition() );
	}
}
