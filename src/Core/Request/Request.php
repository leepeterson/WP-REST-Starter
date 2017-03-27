<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Core\Response;

use GuzzleHttp\Psr7\Uri;
use Inpsyde\WPRESTStarter\Common\HTTPMessage;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * PSR-7-compliant WordPress REST request implementation.
 *
 * @package Inpsyde\WPRESTStarter\Core\Request
 * @since   3.0.0
 */
final class Request extends \WP_REST_Request implements RequestInterface {

	use HTTPMessage;

	/**
	 * HTTP methods (@see \WP_REST_Server::ALLMETHODS).
	 *
	 * @since 3.0.0
	 *
	 * @var string[]
	 */
	const METHODS = [
		'DELETE',
		'GET',
		'PATCH',
		'POST',
		'PUT',
	];

	/**
	 * @var string
	 */
	private $requestTarget;

	/**
	 * @var UriInterface
	 */
	private $uri;

	/**
	 * Constructor. Sets up the properties.
	 *
	 * @since 3.0.0
	 *
	 * @param string $method           Optional. Request method. Defaults to empty string.
	 * @param string $route            Optional. Request route. Defaults to empty string.
	 * @param array  $attributes       Optional. Request attributes. Defaults to empty array.
	 * @param array  $headers          Optional. HTTP header map. Defaults to empty array.
	 * @param mixed  $body             Optional. Request body. Defaults to null.
	 * @param string $protocol_version Optional. HTTP protocol version. Defaults to '1.1'.
	 */
	public function __construct(
		string $method = '',
		string $route = '',
		array $attributes = [],
		array $headers = [],
		$body = null,
		string $protocol_version = '1.1'
	) {

		parent::__construct( $method, $route, $attributes );

		$this->uri = new Uri( \rest_url( $route ) );

		$this->set_headers( $headers );

		if ( ! $this->hasHeader( 'Host' ) ) {
			$this->set_host_from_uri();
		}

		$this->set_stream_for_data( $body );

		$this->protocol_version = $protocol_version;
	}

	/**
	 * Returns an instance based on the given WordPress REST request object.
	 *
	 * @since 3.0.0
	 *
	 * @param \WP_REST_Request $request WordPress REST request object.
	 * @param array            $headers Optional. Request headers. Defaults to empty array.
	 * @param mixed            $body    Optional. Request body. Defaults to null.
	 * @param string           $version Optional. Protocol version. Defaults to '1.1'.
	 *
	 * @return Request
	 */
	public static function from_wp_rest_request(
		\WP_REST_Request $request,
		array $headers = [],
		$body = null,
		string $version = '1.1'
	) {

		return new static(
			$request->get_method(),
			(string) $request->get_route(),
			(array) $request->get_attributes(),
			$headers,
			$body,
			$version
		);
	}

	/**
	 * Returns the message's request target.
	 *
	 * @since 3.0.0
	 *
	 * @return string Request target.
	 */
	public function getRequestTarget() {

		if ( isset( $this->requestTarget ) ) {
			return $this->requestTarget;
		}

		$target = $this->uri->getPath() ?: '/';

		$query = $this->uri->getQuery();
		if ( '' !== $query ) {
			$target .= "?{$query}";
		}

		return $target;
	}

	/**
	 * Returns an instance with the given request target.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $requestTarget Request target.
	 *
	 * @return Request
	 */
	public function withRequestTarget( $requestTarget ) {

		if ( \preg_match( '~\s~', $requestTarget ) ) {
			throw new \InvalidArgumentException(
				'Invalid request target passed to ' . __METHOD__ . '; cannot contain whitespace.'
			);
		}

		$clone = clone $this;

		$clone->requestTarget = $requestTarget;

		return $clone;
	}

	/**
	 * Returns the HTTP method of the request.
	 *
	 * @since 3.0.0
	 *
	 * @return string Request method.
	 */
	public function getMethod() {

		return $this->method;
	}

	/**
	 * Returns an instance with the provided HTTP method.
	 *
	 * @since 3.0.0
	 *
	 * @param string $method Case-sensitive method.
	 *
	 * @return Request
	 *
	 * @throws \InvalidArgumentException if the HTTP method is invalid.
	 */
	public function withMethod( $method ) {

		$method = \strtoupper( $method );

		/**
		 * Filters the allowed HTTP request methods.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $methods Allowed HTTP request methods.
		 * @param Request  $request Request object.
		 */
		$allowed_methods = (array) \apply_filters( 'wp_rest_starter.allowed_request_methods', self::METHODS, $this );

		if ( ! \in_array( $method, $allowed_methods, true ) ) {
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires a valid HTTP method as first argument.'
			);
		}

		if ( $method === $this->method ) {
			return $this;
		}

		$clone = clone $this;
		$clone->set_method( $method );

		return $clone;
	}

	/**
	 * Returns the URI instance.
	 *
	 * @since 3.0.0
	 *
	 * @return UriInterface URI of the request.
	 */
	public function getUri() {

		return $this->uri;
	}

	/**
	 * Returns an instance with the provided URI.
	 *
	 * @since 3.0.0
	 *
	 * @param UriInterface $uri          New request URI to use.
	 * @param bool         $preserveHost Optional. Preserve the original state of the Host header? Defaults to false.
	 *
	 * @return Request
	 *
	 * @throws \InvalidArgumentException if the given URI is invalid.
	 */
	public function withUri( UriInterface $uri, $preserveHost = false ) {

		if ( $uri === $this->uri ) {
			return $this;
		}

		$wp_rest_request = \WP_REST_Request::from_url( $this->uri_to_string( $uri ) );
		if ( ! $wp_rest_request ) {
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires a valid URI as first argument.'
			);
		}

		$request = self::from_wp_rest_request( $wp_rest_request );
		$request->set_headers( $this->headers );

		$request->protocol_version = $this->protocol_version;

		if ( isset( $this->requestTarget ) ) {
			$request->requestTarget = $this->requestTarget;
		}

		if ( $this->stream ) {
			$request->body = $this->stream->getContents();

			$request->stream = $this->stream;
		}

		$request->uri = $uri;

		if ( ! $preserveHost ) {
			$request->set_host_from_uri();
		}

		return $request;
	}

	/**
	 * Sets the body content.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $data Data from the request body.
	 *
	 * @return void
	 */
	public function set_body( $data ) {

		unset( $this->stream );

		parent::set_body( $data );
	}

	/**
	 * Sets the host based on the URI.
	 *
	 * @return void
	 */
	private function set_host_from_uri() {

		$host = $this->uri->getHost();
		if ( '' === $host ) {
			return;
		}

		$port = $this->uri->getPort();
		if ( null !== $port ) {
			$host .= ":{$port}";
		}

		if ( isset( $this->header_names['host'] ) ) {
			$header = $this->header_names['host'];
		} else {
			$header = 'Host';

			$this->header_names['host'] = 'Host';
		}

		// Ensure Host is the first header.
		$this->headers = [ $header => [ $host ] ] + $this->headers;
	}

	/**
	 * Returns the string representation of the given URI object.
	 *
	 * @param UriInterface $uri URI instance.
	 *
	 * @return string URI in string form.
	 */
	private function uri_to_string( UriInterface $uri ) {

		$uri_string = $uri->getHost();
		if ( $uri_string ) {
			$uri_string = \rtrim( $uri->getScheme() . "://{$uri_string}", '/' ) . '/';
		}

		$uri_string .= $uri->getPath();

		$query = $uri->getQuery();
		if ( $query ) {
			$uri_string .= "?{$query}";
		}

		$fragment = $uri->getFragment();
		if ( $fragment ) {
			$uri_string .= "#{$fragment}";
		}

		return $uri_string;
	}
}
