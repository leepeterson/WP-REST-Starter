<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Core\Field;

use Inpsyde\WPRESTStarter\Core\Field\Access as Testee;
use Inpsyde\WPRESTStarter\Tests\Unit\TestCase;

/**
 * Test case for the field access class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit\Core\Field
 * @since   1.0.0
 */
class AccessTest extends TestCase {

	/**
	 * Tests the expected value is returned, if present.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_fields_returns_expected_value() {

		$resource = 'resource';

		$fields = [ 'some', 'fields', 'here' ];

		$GLOBALS['wp_rest_additional_fields'][ $resource ] = $fields;

		self::assertSame( $fields, ( new Testee() )->get_fields( $resource ) );
	}

	/**
	 * Tests an empty array is returned if there are no fields registered for the given resource.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_fields_returns_empty_array() {

		$resource = 'resource';

		unset( $GLOBALS['wp_rest_additional_fields'][ $resource ] );

		self::assertSame( [], ( new Testee() )->get_fields( $resource ) );
	}
}
