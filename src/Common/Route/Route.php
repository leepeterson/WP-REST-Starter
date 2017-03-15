<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Common\Route;

/**
 * Interface for all route implementations.
 *
 * @package Inpsyde\WPRESTStarter\Common\Route
 * @since   1.0.0
 */
interface Route {

	/**
	 * Returns an array of options for the endpoint, or an array of arrays for multiple methods.
	 *
	 * @see   register_rest_route()
	 * @since 1.0.0
	 *
	 * @return array Route options.
	 */
	public function get_options(): array;

	/**
	 * Returns the base URL of the route.
	 *
	 * @see   register_rest_route()
	 * @since 1.0.0
	 *
	 * @return string Base URL of the route.
	 */
	public function get_url(): string;
}
