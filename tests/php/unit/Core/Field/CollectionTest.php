<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Core\Field;

use Inpsyde\WPRESTStarter\Common\Field\Field;
use Inpsyde\WPRESTStarter\Core\Field\Collection as Testee;
use Inpsyde\WPRESTStarter\Tests\Unit\TestCase;

/**
 * Test case for the field collection class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit\Core\Field
 * @since   1.0.0
 */
class CollectionTest extends TestCase {

	/**
	 * Tests the class instance is returned.
	 *
	 * Only test the "fluent" part of the method. The "functional" part is covered in the according integration test.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_add_returns_this() {

		$testee = new Testee();

		$field = \Mockery::mock( Field::class );
		$field->shouldReceive( 'get_name' );

		self::assertSame( $testee, $testee->add( '', $field ) );
	}

	/**
	 * Tests the class instance is returned.
	 *
	 * Only test the "fluent" part of the method. The "functional" part is covered in the according integration test.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_delete_returns_this() {

		$testee = new Testee();

		self::assertSame( $testee, $testee->delete( '', '' ) );
	}

	/**
	 * Tests returning an empty fields array.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Use `Testee::getIterator()` instead of deprecated `Testee::to_array()`.
	 *
	 * @return void
	 */
	public function test_getting_empty_array() {

		$fields = iterator_to_array( ( new Testee() )->getIterator() );

		self::assertSame( [], $fields );
	}
}
