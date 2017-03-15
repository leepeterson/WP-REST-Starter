<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Common\Route;

use Inpsyde\WPRESTStarter\Common\Arguments;

/**
 * Interface for all implementations of extensible route options.
 *
 * @package Inpsyde\WPRESTStarter\Common\Route
 * @since   1.1.0
 */
interface ExtensibleOptions extends Arguments {

	/**
	 * Adds the given options object or array as new entry to the internal options.
	 *
	 * @since 1.1.0
	 *
	 * @param Arguments|array $options Options object or array.
	 *
	 * @return ExtensibleOptions Options object.
	 */
	public function add( $options ): ExtensibleOptions;
}
