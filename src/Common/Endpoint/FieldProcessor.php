<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Common\Endpoint;

/**
 * Interface for all field processor implementations.
 *
 * @package Inpsyde\WPRESTStarter\Common\Endpoint
 * @since   2.0.0
 */
interface FieldProcessor {

	/**
	 * Returns the given properties with added data of all schema-aware fields registered for the given object type.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $properties  Schema properties definition.
	 * @param string $object_type Object type.
	 *
	 * @return array Properties with added data of all schema-aware fields registered for the given object type.
	 */
	public function get_extended_properties( array $properties, $object_type );
}
