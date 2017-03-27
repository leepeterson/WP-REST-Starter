<?php # -*- coding: utf-8 -*-

declare( strict_types = 1 );

namespace Inpsyde\WPRESTStarter\Core\Response;

use Inpsyde\WPRESTStarter\Common\HTTPMessage;
use Psr\Http\Message\ResponseInterface;

/**
 * PSR-7-compliant WordPress REST response implementation.
 *
 * @package Inpsyde\WPRESTStarter\Core\Response
 * @since   3.0.0
 */
final class Response extends \WP_REST_Response implements ResponseInterface {

	use HTTPMessage;

	/**
	 * Map of standard HTTP status code => reason phrase.
	 *
	 * @since 3.0.0
	 *
	 * @var string[]
	 */
	const REASON_PHRASES = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-status',
		208 => 'Already Reported',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Switch Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Time-out',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Large',
		415 => 'Unsupported Media Type',
		416 => 'Requested range not satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		425 => 'Unordered Collection',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		451 => 'Unavailable For Legal Reasons',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Time-out',
		505 => 'HTTP Version not supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		511 => 'Network Authentication Required',
	];

	/**
	 * WordPress stores header values as comma-separated strings (@see \WP_REST_Response::$headers). PSR-7 requires an
	 * associative array with header names as keys, and arrays of header values as values. This is it.
	 *
	 * @var string[][]
	 */
	private $header_map;

	/**
	 * @var string
	 */
	private $reason_phrase;

	/**
	 * Constructor. Sets up the properties.
	 *
	 * @since  3.0.0
	 *
	 * @param mixed  $data             Optional. Response data. Defaults to null.
	 * @param int    $status           Optional. HTTP status code. Defaults to 200.
	 * @param array  $headers          Optional. HTTP header map. Defaults to empty array.
	 * @param string $protocol_version Optional. HTTP protocol version. Defaults to '1.1'.
	 * @param string $reason_phrase    Optional. Reason phrase. Defaults based on status code.
	 */
	public function __construct(
		$data = null,
		int $status = 200,
		array $headers = [],
		string $protocol_version = '1.1',
		string $reason_phrase = ''
	) {

		parent::__construct( $data, $status, $headers );

		// This is only necessary because the parent constructor doesn't use set_data() but writes the $data property.
		$this->set_stream_for_data( $this->data );

		$this->protocol_version = $protocol_version;

		$this->set_reason_phrase( $reason_phrase );
	}

	/**
	 * Returns an instance based on the given WordPress REST response object.
	 *
	 * @since 3.0.0
	 *
	 * @param \WP_REST_Response $response         WordPress REST response object.
	 * @param string            $protocol_version Optional. HTTP protocol version. Defaults to '1.1'.
	 * @param string            $reason_phrase    Optional. Reason phrase. Defaults based on status code.
	 *
	 * @return Response
	 */
	public static function from_wp_rest_response(
		\WP_REST_Response $response,
		string $protocol_version = '1.1',
		string $reason_phrase = ''
	) {

		return new self(
			$response->get_data(),
			$response->get_status(),
			$response->get_headers(),
			$protocol_version,
			$reason_phrase
		);
	}

	/**
	 * Returns the response status code.
	 *
	 * @since 3.0.0
	 *
	 * @return int Status code.
	 */
	public function getStatusCode() {

		return $this->status;
	}

	/**
	 * Returns an instance with the specified status code and, optionally, reason phrase.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $status        HTTP status code.
	 * @param string $reason_phrase Optional. Reason phrase. Defaults based on status code.
	 *
	 * @return Response
	 *
	 * @throws \InvalidArgumentException if the given status code is invalid.
	 */
	public function withStatus( $status, $reason_phrase = '' ) {

		if ( ! \is_numeric( $status ) || ! \array_key_exists( (int) $status, self::REASON_PHRASES ) ) {
			throw new \InvalidArgumentException( "{$status} is no valid HTTP status code." );
		}

		$clone = clone $this;
		$clone->set_status( $status );
		$clone->set_reason_phrase( (string) $reason_phrase );

		return $clone;
	}

	/**
	 * Returns the response reason phrase associated with the status code.
	 *
	 * @since 3.0.0
	 *
	 * @return string Reason phrase.
	 */
	public function getReasonPhrase() {

		return $this->reason_phrase;
	}

	/**
	 * Sets the response data.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $data Response data.
	 *
	 * @return void
	 */
	public function set_data( $data ) {

		if ( $this->is_locked() ) {
			return;
		}

		parent::set_data( $data );

		$this->set_stream_for_data( $this->data );
	}

	/**
	 * Sets the given headers.
	 *
	 * @since 3.0.0
	 *
	 * @param array $headers HTTP header map.
	 *
	 * @return void
	 */
	public function set_headers( $headers ) {

		$this->header_map = [];

		$this->header_names = [];

		foreach ( (array) $headers as $name => $header ) {
			$normalized_name = \strtolower( $name );

			$header = $this->normalize_header( $header );

			if ( isset( $this->header_names[ $normalized_name ] ) ) {
				$name = $this->header_names[ $normalized_name ];

				$header = \array_merge( $this->header_map[ $name ], $header );
			} else {
				$this->header_names[ $normalized_name ] = $name;
			}

			$this->header_map[ $name ] = $header;
		}

		$headers = array_map( function ( array $header ) {

			return implode( ', ', $header );
		}, $this->header_map );

		parent::set_headers( $headers );
	}

	/**
	 * Sets a single HTTP header.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name    Header name.
	 * @param string $value   Header value.
	 * @param bool   $replace Optional. Whether to replace an existing header of the same name. Defaults to true.
	 *
	 * @return void
	 */
	public function header( $name, $value, $replace = true ) {

		$normalized_name = \strtolower( $name );

		$value = $this->normalize_header_value( $value );

		if ( isset( $this->header_names[ $normalized_name ] ) ) {
			$name = $this->header_names[ $normalized_name ];
		} else {
			$this->header_names[ $normalized_name ] = $name;
		}

		parent::header( $name, $value, $replace );

		if ( isset( $this->header_names[ $normalized_name ] ) && ! $replace ) {
			$value = array_merge( $this->header_map[ $name ], (array) $value );
		}

		$this->header_map[ $name ] = (array) $value;
	}

	/**
	 * Sets the given reason phrase, if any, or based on the status code.
	 *
	 * @param string $reason_phrase Reason phrase.
	 *
	 * @return void
	 */
	private function set_reason_phrase( string $reason_phrase ) {

		$this->reason_phrase = ( '' === $reason_phrase && \array_key_exists( $this->status, self::REASON_PHRASES ) )
			? self::REASON_PHRASES[ $this->status ]
			: $reason_phrase;
	}

	/**
	 * Normalizes (i.e., casts to string, and trims) all values of the given header.
	 *
	 * @param array|string $header Raw header values.
	 *
	 * @return string[] Normalized header values.
	 */
	private function normalize_header( $header ) {

		if ( ! \is_array( $header ) ) {
			$header = \explode( ',', $header );
		}

		return \array_map( [ $this, 'normalize_header_value' ], $header );
	}

	/**
	 * Normalizes (i.e., casts to string, and trims) all values of the given header.
	 *
	 * @param mixed $value Raw header value.
	 *
	 * @return string Normalized header value.
	 */
	private function normalize_header_value( $value ) {

		return \trim( $value, " \t" );
	}
}
