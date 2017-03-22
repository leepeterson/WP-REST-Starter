<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Factory;

use Inpsyde\WPRESTStarter\Factory\ResponseFactory as Testee;
use Inpsyde\WPRESTStarter\Tests\Unit\TestCase;

/**
 * Test case for the response factory class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit\Factory
 * @since   2.0.0
 */
class ResponseFactoryTest extends TestCase {

	/**
	 * Tests creating an instance of the default class.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_creating_instance_of_default_class() {

		self::assertInstanceOf( \WP_REST_Response::class, ( new Testee() )->create() );
	}

	/**
	 * Tests creating an instance of the given (base) class.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_creating_instance_of_given_base_class() {

		$class = \WP_REST_Response::class;

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
		$class = \CustomResponse::class;

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
		$class = \CustomResponse::class;

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

		$class = \WP_REST_Response::class;

		/** @noinspection PhpUndefinedClassInspection */
		self::assertInstanceOf( $class, ( new Testee( \CustomResponse::class ) )->create( [], $class ) );
	}
}
