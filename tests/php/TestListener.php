<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde;

use PHPUnit\Framework\BaseTestListener;
use PHPUnit\Framework\TestSuite;

/**
 * Test listener implementation taking care of test-suite-specific tasks.
 *
 * @since 1.0.0
 */
final class TestListener extends BaseTestListener {

	/**
	 * Performs individual test-suite-specific actions.
	 *
	 * This gets triggered by PHPUnit when a new test suite gets run.
	 *
	 * @since 1.0.0
	 *
	 * @param TestSuite $suite Test suite object.
	 *
	 * @return void
	 */
	public function startTestSuite( TestSuite $suite ) {

		switch ( strtolower( $suite->getName() ) ) {
			case 'unit':
				$this->autoload_stubs();
				break;

			case 'integration':
				$this->autoload_stubs();
				break;
		}
	}

	/**
	 * Registers a PSR-4-compliant SPL autoloader for stubs. The global namespace is mapped to the stubs dir.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function autoload_stubs() {

		$dir = __DIR__ . '/_stubs/';

		spl_autoload_register( function ( $fqn ) use ( $dir ) {

			$file_path = $dir . str_replace( '\\', '/', ltrim( $fqn, '\\' ) ) . '.php';
			if ( is_readable( $file_path ) ) {
				/** @noinspection PhpIncludeInspection
				 * The according file for the desired stub.
				 */
				require_once $file_path;

				return true;
			}

			return false;
		} );
	}
}
