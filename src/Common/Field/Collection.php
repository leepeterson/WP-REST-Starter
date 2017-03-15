<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Common\Field;

use IteratorAggregate;

/**
 * Interface for all field collection implementations.
 *
 * @package Inpsyde\WPRESTStarter\Common\Field
 * @since   1.0.0
 * @since   1.1.0 Removed `to_array()` method.
 */
interface Collection extends IteratorAggregate {

	/**
	 * Adds the given field object to the resource with the given name to the collection.
	 *
	 * @since 1.0.0
	 *
	 * @param string $resource Resource name to add the field to.
	 * @param Field  $field    Field object.
	 *
	 * @return static Collection object.
	 */
	public function add( $resource, Field $field );

	/**
	 * Deletes the field object with the given name from the resource with the given name from the collection.
	 *
	 * @since 1.0.0
	 *
	 * @param string $resource   Resource name to delete the field from.
	 * @param string $field_name Field name.
	 *
	 * @return static Collection object.
	 */
	public function delete( $resource, $field_name );
}
