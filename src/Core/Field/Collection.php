<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Core\Field;

use ArrayIterator;
use Inpsyde\WPRESTStarter\Common;

/**
 * Traversable field collection implementation using an array iterator.
 *
 * @package Inpsyde\WPRESTStarter\Core\Field
 * @since   1.0.0
 * @since   1.1.0 Deprecated `to_array()` method.
 * @since   2.0.0 Made the class final.
 * @since   2.0.0 Removed `to_array()` method.
 */
final class Collection implements Common\Field\Collection {

	/**
	 * @var Common\Field\Field[][]
	 */
	private $fields = [];

	/**
	 * Adds the given field object to the resource with the given name to the collection.
	 *
	 * @since 1.0.0
	 *
	 * @param string             $resource Resource name to add the field to.
	 * @param Common\Field\Field $field    Field object.
	 *
	 * @return static Collection object.
	 */
	public function add( $resource, Common\Field\Field $field ) {

		$this->fields[ $resource ][ $field->get_name() ] = $field;

		return $this;
	}

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
	public function delete( $resource, $field_name ) {

		unset( $this->fields[ $resource ][ $field_name ] );

		return $this;
	}

	/**
	 * Returns an iterator object for the internal fields array.
	 *
	 * @since 1.0.0
	 *
	 * @return ArrayIterator Iterator object.
	 */
	public function getIterator() {

		return new ArrayIterator( $this->fields );
	}
}
