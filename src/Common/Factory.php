<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Common;

/**
 * Interface for all factory implementations.
 *
 * @package Inpsyde\WPRESTStarter\Common
 * @since   1.0.0
 */
interface Factory {

	/**
	 * Returns a new object of the given (or default) class, instantiated with the given arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args  Optional. Constructor arguments. Defaults to empty array.
	 * @param string $class Optional. Fully qualified class name. Defaults to empty string.
	 *
	 * @return object Object of the given (or default) class, instantiated with the given arguments.
	 */
	public function create( array $args = [], string $class = '' );
}
