<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Common\Route;

/**
 * Interface for all route registry implementations.
 *
 * @package Inpsyde\WPRESTStarter\Common\Route
 * @since   1.0.0
 */
interface Registry {

	/**
	 * Registers the given routes.
	 *
	 * @since 1.0.0
	 *
	 * @param Collection $routes Route collection object.
	 *
	 * @return void
	 */
	public function register_routes( Collection $routes );
}
