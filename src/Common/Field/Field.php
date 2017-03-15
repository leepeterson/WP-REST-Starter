<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Common\Field;

/**
 * Interface for all field implementations.
 *
 * @package Inpsyde\WPRESTStarter\Common\Field
 * @since   1.0.0
 */
interface Field {

	/**
	 * Returns the field definition (i.e., callbacks and schema).
	 *
	 * @see   register_rest_field()
	 * @since 1.0.0
	 *
	 * @return array Field definition.
	 */
	public function get_definition();

	/**
	 * Returns the name of the field.
	 *
	 * @see   register_rest_field()
	 * @since 1.0.0
	 *
	 * @return string Field name.
	 */
	public function get_name();
}
