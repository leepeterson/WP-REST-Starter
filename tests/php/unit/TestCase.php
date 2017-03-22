<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Abstract base class for all unit test case implementations.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit
 * @since   1.0.0
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase {

	use MockeryPHPUnitIntegration;

	/**
	 * Prepares the test environment before each test.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setUp() {

		parent::setUp();
		Monkey::setUpWP();
	}

	/**
	 * Cleans up the test environment after each test.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function tearDown() {

		Monkey::tearDownWP();
		parent::tearDown();
	}
}
