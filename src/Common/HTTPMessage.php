<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Common;

use Psr\Http\Message\StreamInterface;

use function GuzzleHttp\Psr7\stream_for;

/**
 * Trait to be used by all (PSR-7-compliant) HTTP messages.
 *
 * {@internal instanceof checks are because the WordPress requests and responses don't really have anything in common.}
 *
 * @package Inpsyde\WPRESTStarter\Common
 * @since   3.0.0
 */
trait HTTPMessage {

	/**
	 * @var string[]
	 */
	private $header_names;

	/**
	 * @var bool
	 */
	private $is_locked = false;

	/**
	 * @var string
	 */
	private $protocol_version;

	/**
	 * @var StreamInterface
	 */
	private $stream;

	/**
	 * Returns all message header values.
	 *
	 * @since 3.0.0
	 *
	 * @return string[][] Associative array with header names as keys, and arrays of header values as values.
	 */
	public function getHeaders() {

		return $this instanceof \WP_REST_Request ? $this->headers : $this->header_map;
	}

	/**
	 * Returns a message header value by the given case-insensitive name.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name Case-insensitive header field name.
	 *
	 * @return string[] An array of string values as provided for the given header.
	 */
	public function getHeader( $name ) {

		$name = \strtolower( $name );

		$headers = $this->getHeaders();

		return isset( $headers[ $this->header_names[ $name ] ] )
			? $headers[ $this->header_names[ $name ] ]
			: [];
	}

	/**
	 * Returns a comma-separated string of the values for a single header.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name Case-insensitive header field name.
	 *
	 * @return string A string of values as provided for the given header concatenated together using a comma.
	 */
	public function getHeaderLine( $name ) {

		$header = $this->getHeader( $name );

		return $header ? \implode( ',', $header ) : '';
	}

	/**
	 * Returns an instance without the specified header.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name Case-insensitive header field name to remove.
	 *
	 * @return static
	 */
	public function withoutHeader( $name ) {

		$normalized_name = \strtolower( $name );

		if ( ! isset( $this->header_names[ $normalized_name ] ) ) {
			return $this;
		}

		$name = $this->header_names[ $normalized_name ];

		$clone = clone $this;

		unset( $clone->header_names[ $normalized_name ] );

		if ( $clone instanceof \WP_REST_Request ) {
			$clone->remove_header( $name );
		} else {
			unset(
				$clone->header_map[ $name ],
				$clone->headers[ $name ]
			);
		}

		return $clone;
	}

	/**
	 * Checks if a header exists by the given case-insensitive name.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name Case-insensitive header field name.
	 *
	 * @return bool Whether or not any header names match the given one using a case-insensitive string comparison.
	 */
	public function hasHeader( $name ) {

		return isset( $this->header_names[ \strtolower( $name ) ] );
	}

	/**
	 * Returns an instance with the specified header appended with the given value.
	 *
	 * @since 3.0.0
	 *
	 * @param string          $name  Case-insensitive header field name to add.
	 * @param string|string[] $value Header value(s).
	 *
	 * @return static
	 *
	 * @throws \InvalidArgumentException if the given header name is invalid.
	 * @throws \InvalidArgumentException if the given header values are invalid.
	 */
	public function withAddedHeader( $name, $value ) {

		if ( ! \is_scalar( $name ) ) {
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires a valid header name as first argument.'
			);
		}

		if (
			! \is_scalar( $value )
			&& ! ( \is_array( $value ) && \array_filter( $value, '\is_scalar' ) )
		) {
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires one or more valid header values as second argument.'
			);
		}

		$clone = clone $this;

		if ( $clone instanceof \WP_REST_Request ) {
			$clone->add_header( $name, $value );
		} else {
			$clone->header( $name, $value, false );
		}

		return $clone;
	}

	/**
	 * Returns an instance with the provided value replacing the specified header.
	 *
	 * @since 3.0.0
	 *
	 * @param string          $name  Case-insensitive header field name.
	 * @param string|string[] $value Header value(s).
	 *
	 * @return static
	 *
	 * @throws \InvalidArgumentException if the given header name is invalid.
	 * @throws \InvalidArgumentException if the given header values are invalid.
	 */
	public function withHeader( $name, $value ) {

		if ( ! \is_scalar( $name ) ) {
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires a valid header name as first argument.'
			);
		}

		if (
			! \is_scalar( $value )
			&& ! ( \is_array( $value ) && \array_filter( $value, '\is_scalar' ) )
		) {
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires one or more valid header values as second argument.'
			);
		}

		$clone = clone $this;

		if ( $clone instanceof \WP_REST_Request ) {
			$clone->set_header( $name, $value );
		} else {
			$clone->header( $name, $value );
		}

		return $clone;
	}

	/**
	 * Returns the body of the message.
	 *
	 * @since 3.0.0
	 *
	 * @return StreamInterface The body as a stream.
	 */
	public function getBody() {

		if ( ! $this->stream ) {
			$this->stream = stream_for( '' );
		}

		return $this->stream;
	}

	/**
	 * Returns an instance with the specified message body.
	 *
	 * @since 3.0.0
	 *
	 * @param StreamInterface $body Body.
	 *
	 * @return static
	 */
	public function withBody( StreamInterface $body ) {

		if ( $body === $this->stream ) {
			return $this;
		}

		$clone = clone $this;

		$clone->stream = $body;

		$clone->set_data_from_stream( $body );

		return $clone;
	}

	/**
	 * Returns the HTTP protocol version.
	 *
	 * @since 3.0.0
	 *
	 * @return string HTTP protocol version.
	 */
	public function getProtocolVersion() {

		return $this->protocol_version;
	}

	/**
	 * Returns an instance with the specified HTTP protocol version.
	 *
	 * @since 3.0.0
	 *
	 * @param string $protocol_version HTTP protocol version.
	 *
	 * @return static
	 */
	public function withProtocolVersion( $protocol_version ) {

		$protocol_version = (string) $protocol_version;
		if ( $protocol_version === $this->protocol_version ) {
			return $this;
		}

		$clone = clone $this;

		$clone->protocol_version = $protocol_version;

		return $clone;
	}

	/**
	 * Sets the (body) data included in the given stream.
	 *
	 * @param StreamInterface $stream Stream object.
	 *
	 * @return void
	 */
	private function set_data_from_stream( StreamInterface $stream ) {

		$this->lock();

		if ( $this instanceof \WP_REST_Request ) {
			$this->set_body( $stream->getContents() );

			$this->unlock();

			return;
		}

		$this->set_data( $stream->getContents() );

		$this->unlock();
	}

	/**
	 * Sets the body stream according to the given data.
	 *
	 * @param mixed $data Data to be used for the body stream.
	 *
	 * @return void
	 */
	private function set_stream_for_data( $data ) {

		if ( '' === $data || null === $data ) {
			unset( $this->stream );

			return;
		}

		$this->stream = stream_for( $data );
	}

	/**
	 * Returns if the message is locked due to ongoing processing.
	 *
	 * @return bool
	 */
	private function is_locked() {

		return $this->is_locked;
	}

	/**
	 * Locks the message due to ongoing processing.
	 *
	 * @return void
	 */
	private function lock() {

		$this->is_locked = true;
	}

	/**
	 * Unlocks the message.
	 *
	 * @return void
	 */
	private function unlock() {

		$this->is_locked = false;
	}
}
