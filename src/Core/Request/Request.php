<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Core\Request;

use GuzzleHttp\Psr7\Uri;
use Inpsyde\WPRESTStarter\Common\HTTPMessage;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * PSR-7-compliant WordPress REST request implementation.
 *
 * @package Inpsyde\WPRESTStarter\Core\Request
 * @since   3.0.0
 */
final class Request extends \WP_REST_Request implements ServerRequestInterface {

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
	 * Retrieve server parameters.
	 *
	 * Retrieves data related to the incoming request environment,
	 * typically derived from PHP's $_SERVER superglobal. The data IS NOT
	 * REQUIRED to originate from $_SERVER.
	 *
	 * @return array
	 */
	public function getServerParams() {
		// TODO: Implement getServerParams() method.
	}

	/**
	 * Retrieve cookies.
	 *
	 * Retrieves cookies sent by the client to the server.
	 *
	 * The data MUST be compatible with the structure of the $_COOKIE
	 * superglobal.
	 *
	 * @return array
	 */
	public function getCookieParams() {
		// TODO: Implement getCookieParams() method.
	}

	/**
	 * Return an instance with the specified cookies.
	 *
	 * The data IS NOT REQUIRED to come from the $_COOKIE superglobal, but MUST
	 * be compatible with the structure of $_COOKIE. Typically, this data will
	 * be injected at instantiation.
	 *
	 * This method MUST NOT update the related Cookie header of the request
	 * instance, nor related values in the server params.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated cookie values.
	 *
	 * @param array $cookies Array of key/value pairs representing cookies.
	 *
	 * @return static
	 */
	public function withCookieParams( array $cookies ) {
		// TODO: Implement withCookieParams() method.
	}

	/**
	 * Retrieve query string arguments.
	 *
	 * Retrieves the deserialized query string arguments, if any.
	 *
	 * Note: the query params might not be in sync with the URI or server
	 * params. If you need to ensure you are only getting the original
	 * values, you may need to parse the query string from `getUri()->getQuery()`
	 * or from the `QUERY_STRING` server param.
	 *
	 * @return array
	 */
	public function getQueryParams() {
		// TODO: Implement getQueryParams() method.
	}

	/**
	 * Return an instance with the specified query string arguments.
	 *
	 * These values SHOULD remain immutable over the course of the incoming
	 * request. They MAY be injected during instantiation, such as from PHP's
	 * $_GET superglobal, or MAY be derived from some other value such as the
	 * URI. In cases where the arguments are parsed from the URI, the data
	 * MUST be compatible with what PHP's parse_str() would return for
	 * purposes of how duplicate query parameters are handled, and how nested
	 * sets are handled.
	 *
	 * Setting query string arguments MUST NOT change the URI stored by the
	 * request, nor the values in the server params.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated query string arguments.
	 *
	 * @param array $query Array of query string arguments, typically from
	 *                     $_GET.
	 *
	 * @return static
	 */
	public function withQueryParams( array $query ) {
		// TODO: Implement withQueryParams() method.
	}

	/**
	 * Retrieve normalized file upload data.
	 *
	 * This method returns upload metadata in a normalized tree, with each leaf
	 * an instance of Psr\Http\Message\UploadedFileInterface.
	 *
	 * These values MAY be prepared from $_FILES or the message body during
	 * instantiation, or MAY be injected via withUploadedFiles().
	 *
	 * @return array An array tree of UploadedFileInterface instances; an empty
	 *     array MUST be returned if no data is present.
	 */
	public function getUploadedFiles() {
		// TODO: Implement getUploadedFiles() method.
	}

	/**
	 * Create a new instance with the specified uploaded files.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated body parameters.
	 *
	 * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
	 *
	 * @return static
	 * @throws \InvalidArgumentException if an invalid structure is provided.
	 */
	public function withUploadedFiles( array $uploadedFiles ) {
		// TODO: Implement withUploadedFiles() method.
	}

	/**
	 * Retrieve any parameters provided in the request body.
	 *
	 * If the request Content-Type is either application/x-www-form-urlencoded
	 * or multipart/form-data, and the request method is POST, this method MUST
	 * return the contents of $_POST.
	 *
	 * Otherwise, this method may return any results of deserializing
	 * the request body content; as parsing returns structured content, the
	 * potential types MUST be arrays or objects only. A null value indicates
	 * the absence of body content.
	 *
	 * @return null|array|object The deserialized body parameters, if any.
	 *     These will typically be an array or object.
	 */
	public function getParsedBody() {
		// TODO: Implement getParsedBody() method.
	}

	/**
	 * Return an instance with the specified body parameters.
	 *
	 * These MAY be injected during instantiation.
	 *
	 * If the request Content-Type is either application/x-www-form-urlencoded
	 * or multipart/form-data, and the request method is POST, use this method
	 * ONLY to inject the contents of $_POST.
	 *
	 * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
	 * deserializing the request body content. Deserialization/parsing returns
	 * structured data, and, as such, this method ONLY accepts arrays or objects,
	 * or a null value if nothing was available to parse.
	 *
	 * As an example, if content negotiation determines that the request data
	 * is a JSON payload, this method could be used to create a request
	 * instance with the deserialized parameters.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated body parameters.
	 *
	 * @param null|array|object $data The deserialized body data. This will
	 *                                typically be in an array or object.
	 *
	 * @return static
	 * @throws \InvalidArgumentException if an unsupported argument type is
	 *     provided.
	 */
	public function withParsedBody( $data ) {
		// TODO: Implement withParsedBody() method.
	}

	/**
	 * Retrieve attributes derived from the request.
	 *
	 * The request "attributes" may be used to allow injection of any
	 * parameters derived from the request: e.g., the results of path
	 * match operations; the results of decrypting cookies; the results of
	 * deserializing non-form-encoded message bodies; etc. Attributes
	 * will be application and request specific, and CAN be mutable.
	 *
	 * @return array Attributes derived from the request.
	 */
	public function getAttributes() {
		// TODO: Implement getAttributes() method.
	}

	/**
	 * Retrieve a single derived request attribute.
	 *
	 * Retrieves a single derived request attribute as described in
	 * getAttributes(). If the attribute has not been previously set, returns
	 * the default value as provided.
	 *
	 * This method obviates the need for a hasAttribute() method, as it allows
	 * specifying a default value to return if the attribute is not found.
	 *
	 * @see getAttributes()
	 *
	 * @param string $name    The attribute name.
	 * @param mixed  $default Default value to return if the attribute does not exist.
	 *
	 * @return mixed
	 */
	public function getAttribute( $name, $default = null ) {
		// TODO: Implement getAttribute() method.
	}

	/**
	 * Return an instance with the specified derived request attribute.
	 *
	 * This method allows setting a single derived request attribute as
	 * described in getAttributes().
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated attribute.
	 *
	 * @see getAttributes()
	 *
	 * @param string $name  The attribute name.
	 * @param mixed  $value The value of the attribute.
	 *
	 * @return static
	 */
	public function withAttribute( $name, $value ) {
		// TODO: Implement withAttribute() method.
	}

	/**
	 * Return an instance that removes the specified derived request attribute.
	 *
	 * This method allows removing a single derived request attribute as
	 * described in getAttributes().
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that removes
	 * the attribute.
	 *
	 * @see getAttributes()
	 *
	 * @param string $name The attribute name.
	 *
	 * @return static
	 */
	public function withoutAttribute( $name ) {
		// TODO: Implement withoutAttribute() method.
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
