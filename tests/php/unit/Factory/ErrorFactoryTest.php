<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Factory;

use Inpsyde\WPRESTStarter\Factory\ErrorFactory as Testee;
use Inpsyde\WPRESTStarter\Tests\Unit\TestCase;

/**
 * Test case for the error factory class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit\Factory
 * @since   2.0.0
 */
class ErrorFactoryTest extends TestCase {

	/**
	 * Tests creating an instance of the default class.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_creating_instance_of_default_class() {

		self::assertInstanceOf( \WP_Error::class, ( new Testee() )->create() );
	}

	/**
	 * Tests creating an instance of the given (base) class.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_creating_instance_of_given_base_class() {

		$class = \WP_Error::class;

		self::assertInstanceOf( $class, ( new Testee() )->create( [], $class ) );
	}

	/**
	 * Tests creating an instance of the given class.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_creating_instance_of_given_class() {

		/** @noinspection PhpUndefinedClassInspection */
		$class = \CustomError::class;

		self::assertInstanceOf( $class, ( new Testee() )->create( [], $class ) );
	}

	/**
	 * Tests creating an instance of the given default class.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_creating_instance_of_given_default_class() {

		/** @noinspection PhpUndefinedClassInspection */
		$class = \CustomError::class;

		self::assertInstanceOf( $class, ( new Testee( $class ) )->create() );
	}

	/**
	 * Tests creating an instance of the given class irrelevant of a potential default class.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_creating_instance_of_given_class_irrelevant_of_default_class() {

		$class = \WP_Error::class;

		/** @noinspection PhpUndefinedClassInspection */
		self::assertInstanceOf( $class, ( new Testee( \CustomError::class ) )->create( [], $class ) );
	}
}
