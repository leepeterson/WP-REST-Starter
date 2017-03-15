<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Core\Request;

use Inpsyde\WPRESTStarter\Common;
use Inpsyde\WPRESTStarter\Core;

/**
 * Simple field processor implementation.
 *
 * @package Inpsyde\WPRESTStarter\Core\Request
 * @since   2.0.0
 */
class FieldProcessor implements Common\Request\FieldProcessor {

	/**
	 * @var Common\Field\Access
	 */
	private $field_access;

	/**
	 * Constructor. Sets up the properties.
	 *
	 * @since 2.0.0
	 *
	 * @param Common\Field\Access $field_access Optional. Field access object. Defaults to null.
	 */
	public function __construct( Common\Field\Access $field_access = null ) {

		$this->field_access = $field_access ?? new Core\Field\Access();
	}

	/**
	 * Returns the given object with added data of all registered readable fields.
	 *
	 * @since 2.0.0
	 *
	 * @param array            $object      Object data in array form.
	 * @param \WP_REST_Request $request     Request object.
	 * @param string           $object_type Optional. Object type. Defaults to empty string.
	 *
	 * @return array Object with added data of all registered readable fields.
	 */
	public function add_fields_to_object( array $object, \WP_REST_Request $request, string $object_type = '' ): array {

		$fields = $this->field_access->get_fields( $object_type );
		foreach ( $fields as $name => $definition ) {
			if ( empty( $definition['get_callback'] ) ) {
				continue;
			}

			if ( ! \is_callable( $definition['get_callback'] ) ) {
				if ( \defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					\trigger_error( "Invalid callback. Cannot read {$name} field for {$object_type}." );
				}

				continue;
			}

			$object[ $name ] = $definition['get_callback']( $object, $name, $request, $object_type );
		}

		return $object;
	}

	/**
	 * Updates all registered updatable fields of the given object.
	 *
	 * @since 2.0.0
	 *
	 * @param array            $object      Object data in array form.
	 * @param \WP_REST_Request $request     Request object.
	 * @param string           $object_type Optional. Object type. Defaults to empty string.
	 *
	 * @return int Number of fields updated.
	 */
	public function update_fields_for_object(
		array $object,
		\WP_REST_Request $request,
		string $object_type = ''
	): int {

		$updated = 0;

		$fields = $this->field_access->get_fields( $object_type );
		foreach ( $fields as $name => $definition ) {
			if ( ! isset( $request[ $name ] ) ) {
				continue;
			}

			if ( empty( $definition['update_callback'] ) ) {
				continue;
			}

			if ( ! \is_callable( $definition['update_callback'] ) ) {
				if ( \defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					\trigger_error( "Invalid callback. Cannot update {$name} field for {$object_type}." );
				}

				continue;
			}

			$definition['update_callback']( $request[ $name ], $object, $name, $request, $object_type );

			$updated++;
		}

		return $updated;
	}
}
