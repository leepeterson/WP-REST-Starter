<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Common\Endpoint;

use Inpsyde\WPRESTStarter\Common;

/**
 * Interface for all response data schema implementations.
 *
 * @package Inpsyde\WPRESTStarter\Common\Endpoint
 * @since   1.0.0
 * @since   2.0.0 Added get_title() method.
 */
interface Schema extends Common\Schema {

	/**
	 * Returns the properties of the schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array Properties definition.
	 */
	public function get_properties(): array;

	/**
	 * Returns the title of the schema.
	 *
	 * @since 2.0.0
	 *
	 * @return string Title.
	 */
	public function get_title(): string;
}
