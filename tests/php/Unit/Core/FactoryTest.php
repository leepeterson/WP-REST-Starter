<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Core;

use Inpsyde\WPRESTStarter\Core\Factory as Testee;
use Inpsyde\WPRESTStarter\Tests\TestCase;

/**
 * Test case for the factory class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit\Core
 * @since   2.0.0
 */
class FactoryTest extends TestCase {

	/**
	 * Tests construction with an invalid base fails.
	 *
	 * @since  2.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::__construct()
	 * @expectedException \InvalidArgumentException
	 *
	 * @return void
	 */
	public function test_construction_with_invalid_base_fails() {

		new Testee( '\InvalidFQN' );
	}

	/**
	 * Tests construction with no valid class fails.
	 *
	 * @since  2.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::__construct()
	 * @expectedException \InvalidArgumentException
	 *
	 * @return void
	 */
	public function test_construction_with_no_valid_class_fails() {

		new Testee( '\ArrayAccess' );
	}

	/**
	 * Tests construction with an invalid default class fails.
	 *
	 * @since  2.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::__construct()
	 * @expectedException \Inpsyde\WPRESTStarter\Exception\InvalidClassException
	 *
	 * @return void
	 */
	public function test_construction_with_invalid_default_class_fails() {

		new Testee( '\ArrayAccess', '\InvalidFQN' );
	}

	/**
	 * Tests creation with an invalid class fails.
	 *
	 * @since  2.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::create()
	 * @expectedException \Inpsyde\WPRESTStarter\Exception\InvalidClassException
	 *
	 * @return void
	 */
	public function test_creation_with_invalid_class_fails() {

		( new Testee( '\ArrayObject' ) )->create( [], '\InvalidFQN' );
	}

	/**
	 * Tests statically creating a factory with the default class being the base.
	 *
	 * @since  2.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::with_default_class()
	 *
	 * @return void
	 */
	public function test_creating_factory_with_default_class_being_base() {

		$factory = Testee::with_default_class( __CLASS__, __CLASS__ );

		$this->assertInstanceOf( 'Inpsyde\WPRESTStarter\Core\Factory', $factory );
	}

	/**
	 * Tests statically creating a factory with the default class being different from the base.
	 *
	 * @since  2.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::with_default_class()
	 *
	 * @return void
	 */
	public function test_creating_factory_with_default_class_other_than_base() {

		$factory = Testee::with_default_class( '\PHPUnit\Framework\TestCase', __CLASS__ );

		$this->assertInstanceOf( 'Inpsyde\WPRESTStarter\Core\Factory', $factory );
	}

	/**
	 * Tests creating an instance of the default class from base.
	 *
	 * @since  2.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::create()
	 *
	 * @return void
	 */
	public function test_creating_instance_of_default_class_from_base() {

		$this->assertInstanceOf( __CLASS__, ( new Testee( __CLASS__ ) )->create( [ null ] ) );
	}

	/**
	 * Tests creating an instance of the default class.
	 *
	 * @since  2.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::create()
	 *
	 * @return void
	 */
	public function test_creating_instance_of_default_class() {

		$this->assertInstanceOf( __CLASS__, ( new Testee( '\PHPUnit\Framework\TestCase', __CLASS__ ) )->create() );
	}

	/**
	 * Tests creating an instance of the default (base) class.
	 *
	 * @since  2.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::create()
	 *
	 * @return void
	 */
	public function test_creating_instance_of_default_base_class() {

		$this->assertInstanceOf( __CLASS__, ( new Testee( __CLASS__, __CLASS__ ) )->create( [ null, [] ] ) );
	}

	/**
	 * Tests creating an instance of the given class.
	 *
	 * @since  2.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::create()
	 *
	 * @return void
	 */
	public function test_creating_instance_of_given_class() {

		$this->assertInstanceOf( __CLASS__, ( new Testee( '\PHPUnit\Framework\TestCase' ) )->create( [], __CLASS__ ) );
	}

	/**
	 * Tests creating an instance of the given (base) class.
	 *
	 * @since  2.0.0
	 *
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::__construct()
	 * @covers Inpsyde\WPRESTStarter\Core\Factory::create()
	 *
	 * @return void
	 */
	public function test_creating_instance_of_default_and_base_class() {

		$this->assertInstanceOf( __CLASS__, ( new Testee( __CLASS__ ) )->create( [], __CLASS__ ) );
	}
}
