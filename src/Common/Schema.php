<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Common;

/**
 * Interface for all schema implementations.
 *
 * @package Inpsyde\WPRESTStarter\Common
 * @since   1.0.0
 */
interface Schema {

	/**
	 * Returns the schema definition.
	 *
	 * @since 1.0.0
	 *
	 * @return array Schema definition.
	 */
	public function get_schema();
}
