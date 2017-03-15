<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Factory;

use Closure;

/**
 * Factory for diverse permission callbacks.
 *
 * @package Inpsyde\WPRESTStarter\Factory
 * @since   1.0.0
 */
class PermissionCallback {

	/**
	 * Returns a callback that checks if the current user has all of the given capabilities.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $capabilities Capabilities required to get permission.
	 *
	 * @return Closure Callback that checks if the current user has all of the given capabilities.
	 */
	public function current_user_can( array $capabilities ): Closure {

		/**
		 * Checks if the current user has specific capabilities.
		 *
		 * @since 1.0.0
		 *
		 * @return bool Whether or not the current user has specific capabilities.
		 */
		return function () use ( $capabilities ): bool {

			foreach ( $capabilities as $capability ) {
				if ( ! current_user_can( $capability ) ) {
					return false;
				}
			}

			return true;
		};
	}
}
