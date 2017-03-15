<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Core\Field;

use Inpsyde\WPRESTStarter\Core\Field\Collection as Testee;
use Inpsyde\WPRESTStarter\Tests\TestCase;
use Mockery;

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
	 * @since  1.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Collection::add()
	 *
	 * @return void
	 */
	public function test_add_returns_this() {

		$testee = new Testee();

		$field = Mockery::mock( 'Inpsyde\WPRESTStarter\Common\Field\Field' );
		$field->shouldReceive( 'get_name' );

		$this->assertSame( $testee, $testee->add( '', $field ) );
	}

	/**
	 * Tests the class instance is returned.
	 *
	 * Only test the "fluent" part of the method. The "functional" part is covered in the according integration test.
	 *
	 * @since  1.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Collection::delete()
	 *
	 * @return void
	 */
	public function test_delete_returns_this() {

		$testee = new Testee();

		$this->assertSame( $testee, $testee->delete( '', '' ) );
	}

	/**
	 * Tests returning an empty fields array.
	 *
	 * @since  1.0.0
	 * @since  1.1.0 Use `Testee::getIterator()` instead of deprecated `Testee::to_array()`.
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Field\Collection::getIterator()
	 *
	 * @return void
	 */
	public function test_getting_empty_array() {

		$fields = iterator_to_array( ( new Testee() )->getIterator() );

		$this->assertSame( [], $fields );
	}
}
