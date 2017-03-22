# WP REST Starter

[![Version](https://img.shields.io/packagist/v/inpsyde/wp-rest-starter.svg)](https://packagist.org/packages/inpsyde/wp-rest-starter)
[![Status](https://img.shields.io/badge/status-active-brightgreen.svg)](https://github.com/inpsyde/WP-REST-Starter)
[![Build](https://img.shields.io/travis/inpsyde/WP-REST-Starter.svg)](http://travis-ci.org/inpsyde/WP-REST-Starter)
[![Downloads](https://img.shields.io/packagist/dt/inpsyde/wp-rest-starter.svg)](https://packagist.org/packages/inpsyde/wp-rest-starter)
[![License](https://img.shields.io/packagist/l/inpsyde/wp-rest-starter.svg)](https://packagist.org/packages/inpsyde/wp-rest-starter)

> Starter package for working with the WP REST API in an object-oriented fashion.

![WP REST Starter](resources/images/banner.png)

## Introduction

Since both the infrastructure and the first set of endpoints of the WordPress REST API got merged into Core, it’s obvious for plugin and even theme authors to jump on the bandwagon.
This package provides you with virtually anything you need to start _feeling RESTful_.

WP REST Starter consists of several interfaces for both data types and _business logic_, and it comes with straightforward implementations suitable for the common needs.
All you have to do is configure your REST routes and data structures, and implement the according request handlers.

## Table of Contents

* [Installation](#installation)
  * [Requirements](#requirements)
* [Usage](#usage)
  * [Actions](#actions)
    * [`wp_rest_starter.register_fields`](#wp_rest_starterregister_fields)
    * [`wp_rest_starter.register_routes`](#wp_rest_starterregister_routes)
  * [Registering a Simple Custom Route](#registering-a-simple-custom-route)
  * [Registering a Complex Custom Route](#registering-a-complex-custom-route)
  * [Adding Custom Fields to the Response of Existing Endpoints](#adding-custom-fields-to-the-response-of-existing-endpoints)
  * [Example Endpoint Schema Implementation](#example-endpoint-schema-implementation)
  * [Example Endpoint Arguments Implementation](#example-endpoint-arguments-implementation)
  * [Example Request Handler Implementation](#example-request-handler-implementation)
  * [Example Field Schema Implementation](#example-field-schema-implementation)
  * [Example Field Reader Implementation](#example-field-reader-implementation)
  * [Example Field Updater Implementation](#example-field-updater-implementation)
  * [Example Formatter Implementation](#example-formatter-implementation)

## Installation

Install with [Composer](https://getcomposer.org):

```sh
$ composer require inpsyde/wp-rest-starter
```

Run the tests:

```sh
$ vendor/bin/phpunit
```

### Requirements

This package requires PHP 7 or higher.

Adding custom fields to existing resources requires WordPress 4.7 or higher, or the [WP REST API](https://wordpress.org/plugins/rest-api/) plugin.
If all you want to do is define custom REST routes, you're already good to go with WordPress 4.4 or higher.

## Usage

The following sections will help you get started with the WordPress REST API in an object-oriented fashion.
If you're new to working with the WordPress REST API in general, please refer to [the REST API handbook](https://developer.wordpress.org/rest-api/).

### Actions

In order to inform about certain events, some of the shipped classes provide you with custom actions.
For each of these, a short description as well as a code example on how to _take action_ is given below.

#### `wp_rest_starter.register_fields`

When using the default field registry class, `Inpsyde\WPRESTStarter\Core\Field\Registry`, this action fires right before the fields are registered.

**Arguments:**

- `$fields` [`Inpsyde\WPRESTStarter\Common\Field\Collection`](src/Common/Field/Collection.php): Field collection object.

**Usage Example:**

```php
<?php

use Inpsyde\WPRESTStarter\Common\Field\Collection;

add_action( 'wp_rest_starter.register_fields', function ( Collection $fields ) {

	// Remove a specific field from all post resources.
	$fields->delete( 'post', 'some-field-with-sensible-data' );
} );
```

#### `wp_rest_starter.register_routes`

When using the default route registry class, `Inpsyde\WPRESTStarter\Core\Route\Registry`, this action fires right before the routes are registered.

**Arguments:**

- `$routes` [`Inpsyde\WPRESTStarter\Common\Route\Collection`](src/Common/Route/Collection.php): Route collection object.
- `$namespace` `string`: Namespace.

**Usage Example:**

```php
<?php

use Inpsyde\WPRESTStarter\Common\Route\Collection;
use Inpsyde\WPRESTStarter\Core\Route\Options;
use Inpsyde\WPRESTStarter\Core\Route\Route;

add_action( 'wp_rest_starter.register_routes', function ( Collection $routes, string $namespace ) {

	if ( 'desired-namespace' !== $namespace ) {
		return;
	}

	// Register a custom REST route in the desired namespace.
	$routes->add( new Route(
		'some-custom-route/maybe-even-with-arguments',
		Options::with_callback( 'some_custom_request_handler_callback' )
	) );
}, 10, 2 );
```

### Registering a Simple Custom Route

The very simple example code below illustrates the registration of a custom route with endpoints for reading and creating the individual resource(s).

```php
<?php

use Inpsyde\WPRESTStarter\Core\Route\Collection;
use Inpsyde\WPRESTStarter\Core\Route\Options;
use Inpsyde\WPRESTStarter\Core\Route\Registry;
use Inpsyde\WPRESTStarter\Core\Route\Route;
use Inpsyde\WPRESTStarter\Factory;

add_action( 'rest_api_init', function() {

	// Create a new route collection.
	$routes = new Collection();

	// Optional: Create a permission callback factory.
	$permission = new Factory\PermissionCallback();

	$endpoint_base = 'some-data-type';

	// Set up the request handler (implement ~\Common\Endpoint\RequestHandler).
	$handler = new Some\Custom\ReadRequestHandler( /* ...args */ );

	// Optional: Set up an according endpoint $args object (implement ~\Common\Arguments).
	$args = new Some\Custom\ReadArguments();

	// Register a route for the READ endpoint.
	$routes->add( new Route(
		$endpoint_base . '(?:/(?P<id>\d+))?',
		Options::from_arguments( $handler, $args )
	) );

	// Set up the request handler (implement ~\Common\Endpoint\RequestHandler).
	$handler = new Some\Custom\CreateRequestHandler( /* ...args */ );

	// Optional: Set up an according endpoint $args object (implement ~\Common\Arguments).
	$args = new Some\Custom\CreateArguments();

	// Register a route for the CREATE endpoint.
	$routes->add( new Route(
		$endpoint_base,
		Options::from_arguments( $handler, $args, WP_REST_Server::CREATABLE, [
			// Optional: Set a callback to check permission.
			'permission_callback' => $permission->current_user_can( 'edit_posts', 'custom_cap' ),
		] )
	) );

	// Register all routes in your desired namespace.
	( new Registry( 'some-namespace-here' ) )->register_routes( $routes );
} );
```

### Registering a Complex Custom Route

What follows is a more complete (and thus complex) example of registering a custom route.
The nature of the resource is described by using an according schema object.
Both the endpoint schema object and the request handlers are aware of additional fields registered by other parties for their individual resource.
The response objects also contain links (compact, if supported).

```php
<?php

use Inpsyde\WPRESTStarter\Core\Endpoint;
use Inpsyde\WPRESTStarter\Core\Field;
use Inpsyde\WPRESTStarter\Core\Request;
use Inpsyde\WPRESTStarter\Core\Response;
use Inpsyde\WPRESTStarter\Core\Route\Collection;
use Inpsyde\WPRESTStarter\Core\Route\Options;
use Inpsyde\WPRESTStarter\Core\Route\Registry;
use Inpsyde\WPRESTStarter\Core\Route\Route;
use Inpsyde\WPRESTStarter\Factory;

add_action( 'rest_api_init', function() {

	$namespace = 'some-namespace-here';

	// Create a new route collection.
	$routes = new Collection();

	// Optional: Create a field access object.
	$field_access = new Field\Access();

	// Optional: Create a request field processor object.
	$request_field_processor = new Request\FieldProcessor( $field_access );

	// Optional: Create an endpoint schema field processor object.
	$schema_field_processor = new Endpoint\FieldProcessor( $field_access );

	// Create a permission callback factory.
	$permission = new Factory\PermissionCallback();

	// Create a response data access object.
	$response_data_access = new Response\LinkAwareDataAccess();

	// Create a response factory.
	$response_factory = new Factory\ResponseFactory();

	// Set up a field-aware schema object (implement ~\Common\Endpoint\Schema).
	$schema = new Some\Endpoint\Schema( $schema_field_processor );

	$base = $schema->get_title();

	// Optional: Set up a formatter taking care of data preparation.
	$formatter = new Some\Endpoint\Formatter(
		$schema,
		$namespace,
		new Response\SchemaAwareDataFilter( $schema ),
		$response_factory,
		$response_data_access
	);

	// Register a route for the READ endpoint.
	$routes->add( new Route(
		$base . '(?:/(?P<id>\d+))?',
		Options::from_arguments(
			new Some\Endpoint\ReadRequestHandler(
				$maybe_some_external_api,
				$formatter,
				$schema,
				$request_field_processor,
				$response_factory
			),
			new Some\Endpoint\ReadEndpointArguments()
		)->set_schema( $schema )
	) );

	// Register a route for the CREATE endpoint.
	$routes->add( new Route(
		$base,
		Options::from_arguments(
			new Some\Endpoint\CreateRequestHandler(
				$maybe_some_external_api,
				$formatter,
				$schema,
				$request_field_processor,
				$response_factory
			),
			new Some\Endpoint\CreateEndpointArguments(),
			WP_REST_Server::CREATABLE,
			[
				// Optional: Set a callback to check permission.
				'permission_callback' => $permission->current_user_can( 'edit_posts', 'custom_cap' ),
			]
		)->set_schema( $schema )
	) );

	// Optional: Register a route for the endpoint schema.
	$routes->add( new Route(
		$base . '/schema',
		Options::with_callback( [ $schema, 'get_schema' ] )
	) );

	// Register all routes in your desired namespace.
	( new Registry( $namespace ) )->register_routes( $routes );
} );
```

### Adding Custom Fields to the Response of Existing Endpoints

The below example shows how to register two additional fields to all response objects of the targeted resource.
Of course, the according code that creates the response has to be aware of additional fields.
This is the case when the code uses either the `WP_REST_Controller` class or a (custom) implementation of the field processor interfaces provided in this package.

```php
<?php

use Inpsyde\WPRESTStarter\Core\Field\Collection;
use Inpsyde\WPRESTStarter\Core\Field\Field;
use Inpsyde\WPRESTStarter\Core\Field\Registry;

add_action( 'rest_api_init', function() {

	// Create a new field collection.
	$fields = new Collection();

	// Optional: Set up the field reader (implement ~\Common\Field\Reader).
	$reader = new Some\Field\Reader();

	// Optional: Set up the field updater (implement ~\Common\Field\Updater).
	$updater = new Some\Field\Reader();

	// Optional: Create a field schema (implement ~\Common\Schema).
	$schema = new Some\Field\Schema();

	// Register a read- and updatable field for some resource.
	$fields->add(
		'some-data-type',
		( new Field( 'has_explicit_content' ) )
			->set_get_callback( $reader )
			->set_update_callback( $updater )
			->set_schema( $schema )
	);

	// Set up the field reader (implement ~\Common\Field\Reader).
	$reader = new Some\Field\Reader();

	// Register another read-only field for some resource.
	$fields->add(
		'some-data-type',
		( new Field( 'is_long_read' ) )
			->set_get_callback( $reader )
	);

	// Register all fields.
	( new Registry() )->register_fields( $fields );
} );
```

### Example Endpoint Schema Implementation

The below endpoint schema implementation is aware of fields registered by other parties for the current resource.
By means of an injected (or defaulted) endpoint schema field processor object, the data of all registered schema-aware fields is added to the schema properties.

```php
<?php

use Inpsyde\WPRESTStarter\Common\Endpoint\FieldProcessor;
use Inpsyde\WPRESTStarter\Common\Endpoint\Schema;
use Inpsyde\WPRESTStarter\Core;

class SomeEndpointSchema implements Schema {

	/**
	 * @var FieldProcessor
	 */
	private $field_processor;

	/**
	 * @var string
	 */
	private $title ='some-data-type';

	/**
	 * Constructor. Sets up the properties.
	 *
	 * @param FieldProcessor $field_processor Optional. Field processor object. Defaults to null.
	 */
	public function __construct( FieldProcessor $field_processor = null ) {

		$this->field_processor = $field_processor ?? new Core\Endpoint\FieldProcessor();
	}

	/**
	 * Returns the properties of the schema.
	 *
	 * @return array Properties definition.
	 */
	public function get_properties(): array {

		$properties = [
			'id' => [
				'description' => __( "The ID of the data object.", 'some-text-domain' ),
				'type'        => 'integer',
				'context'     => [ 'view', 'edit' ],
			],
		];

		return $this->field_processor->get_extended_properties( $properties, $this->title );
	}

	/**
	 * Returns the schema definition.
	 *
	 * @return array Schema definition.
	 */
	public function get_schema(): array {

		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->title,
			'type'       => 'object',
			'properties' => $this->get_properties(),
		];
	}

	/**
	 * Returns the schema title.
	 *
	 * @return string Schema title.
	 */
	public function get_title(): string {

		return $this->title;
	}
}
```

### Example Endpoint Arguments Implementation

An endpoint arguments implementation is straightforward, and in most cases only a single method returning a hard-coded array.
The below code also contains a validate callback that returns a `WP_Error` object on failure.

```php
<?php
use Inpsyde\WPRESTStarter\Common\Arguments;
use Inpsyde\WPRESTStarter\Factory\ErrorFactory;

class SomeEndpointArguments implements Arguments {

	/**
	 * @var ErrorFactory
	 */
	private $error_factory;

	/**
	 * Constructor. Sets up the properties.
	 *
	 * @since 1.0.0
	 *
	 * @param ErrorFactory $error_factory Optional. Error factory object. Defaults to null.
	 */
	public function __construct( ErrorFactory $error_factory = null ) {

		$this->error_factory = $error_factory ?? new ErrorFactory();
	}

	/**
	 * Returns the arguments in array form.
	 *
	 * @return array[] Arguments array.
	 */
	public function to_array(): array {

		return [
			'id'   => [
				'description' => __( "The ID of a data object.", 'some-text-domain' ),
				'type'        => 'integer',
				'minimum'     => 1,
				'required'    => true,
				'validate_callback' => function ( $value ) {
					if ( is_numeric( $value ) ) {
						return true;
					}

					return $this->error_factory->create( [
						'no_numeric_id',
						__( "IDs have to be numeric.", 'some-text-domain' ),
						[
							'status' => 400,
						],
					] );
				},
			],
			'type' => [
				'description' => __( "The type of the data object.", 'some-text-domain' ),
				'type'        => 'string',
				'default'     => 'foo',
			],
		];
	}
}
```

### Example Request Handler Implementation

This (update) request handler is aware of additional fields.
It uses an external API to work with the data.
Data preparation is done by a dedicated formatter.

```php
<?php
use Inpsyde\WPRESTStarter\Common\Endpoint;
use Inpsyde\WPRESTStarter\Common\Request\FieldProcessor;
use Inpsyde\WPRESTStarter\Core;
use Inpsyde\WPRESTStarter\Factory\ResponseFactory;
use Some\Endpoint\Formatter;
use Some\External\API;

class SomeRequestHandler implements Endpoint\RequestHandler {

	/**
	 * @var API
	 */
	private $api;

	/**
	 * @var FieldProcessor
	 */
	private $field_processor;

	/**
	 * @var Formatter
	 */
	private $formatter;

	/**
	 * @var string
	 */
	private $object_type;

	/**
	 * @var ResponseFactory
	 */
	private $response_factory;

	/**
	 * Constructor. Sets up the properties.
	 *
	 * @param API             $api              API object.
	 * @param Formatter       $formatter        Formatter object.
	 * @param Endpoint\Schema $schema           Optional. Schema object. Defaults to null.
	 * @param FieldProcessor  $field_processor  Optional. Field processor object. Defaults to null.
	 * @param ResponseFactory $response_factory Optional. Response factory object. Defaults to null.
	 */
	public function __construct(
		API $api,
		Formatter $formatter,
		Endpoint\Schema $schema = null,
		FieldProcessor $field_processor = null,
		ResponseFactory $response_factory = null
	) {

		$this->api = $api;

		$this->formatter = $formatter;

		$this->object_type = $schema ? $schema->get_title() : '';

		$this->field_processor = $field_processor ?? new Core\Request\FieldProcessor();

		$this->response_factory = $response_factory ?? new ResponseFactory();
	}

	/**
	 * Handles the given request object and returns the according response object.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response.
	 */
	public function handle_request( WP_REST_Request $request ): WP_REST_Response {

		$id = $request['id'];

		// Update the according object data by using the injected data API.
		if ( ! $this->api->update_data( $id, $request->get_body_params() ) ) {
			// Ooops! Send an error response.
			return $this->response_factory->create( [
				[
					'code'    => 'could_not_update',
					'message' => __( "The object could not be updated.", 'some-text-domain' ),
					'data'    => $request->get_params(),
				],
				400,
			] );
		}

		// Get the (updated) data from the API.
		$data = $this->api->get_data( $id );

		// Set the request context.
		$context = $request['context'] ?? 'view';

		// Prepare the data for the response.
		$data = $this->formatter->format( $data, $context );

		// Update potential fields registered for the resource.
		$this->field_processor->update_fields_for_object( $data, $request, $this->object_type );

		// Add the data of potential fields registered for the resource.
		$data = $this->field_processor->add_fields_to_object( $data, $request, $this->object_type );

		// Send a response object containing the updated data.
		return $this->response_factory->create( [ $data ] );
	}
}
```

### Example Field Schema Implementation

The schema of a field is not more than a definition in array form.

```php
<?php

use Inpsyde\WPRESTStarter\Common\Schema;

class SomeFieldSchema implements Schema {

	/**
	 * Returns the schema definition.
	 *
	 * @return array Schema definition.
	 */
	public function get_schema(): array {

		return [
			'description' => __( "Whether the object contains explicit content.", 'some-text-domain' ),
			'type'        => 'boolean',
			'context'     => [ 'view', 'edit' ],
		];
	}
}
```

### Example Field Reader Implementation

The below field reader implementation uses a global callback to get the field value.
You could also inject an API object and use provided methods.

```php
<?php

use Inpsyde\WPRESTStarter\Common\Field\Reader;

class SomeFieldReader implements Reader {

	/**
	 * Returns the value of the field with the given name of the given object.
	 *
	 * @param array           $object      Object data in array form.
	 * @param string          $field_name  Field name.
	 * @param WP_REST_Request $request     Request object.
	 * @param string          $object_type Optional. Object type. Defaults to empty string.
	 *
	 * @return mixed Field value.
	 */
	public function get_value(
		array $object,
		string $field_name,
		WP_REST_Request $request,
		string $object_type = ''
	) {

		if ( empty( $object['id'] ) ) {
			return false;
		}

		return (bool) some_field_getter_callback( $object['id'], $field_name );
	}
}
```

### Example Field Updater Implementation

The below field updater implementation uses a global callback to update the field value.
You could also inject an API object and use provided methods.
The injected permission callback, if any, is used to check permission prior to updating the field.

```php
<?php

use Inpsyde\WPRESTStarter\Common\Field\Updater;

class SomeFieldUpdater implements Updater {

	/**
	 * @var callable
	 */
	private $permission_callback;

	/**
	 * Constructor. Sets up the properties.
	 *
	 * @param callable $permission_callback Optional. Permission callback. Defaults to null.
	 */
	public function __construct( $permission_callback = null ) {

		if ( is_callable( $permission_callback ) ) {
			$this->permission_callback = $permission_callback;
		}
	}

	/**
	 * Updates the value of the field with the given name of the given object to the given value.
	 *
	 * @param mixed           $value       New field value.
	 * @param object          $object      Object data.
	 * @param string          $field_name  Field name.
	 * @param WP_REST_Request $request     Optional. Request object. Defaults to null.
	 * @param string          $object_type Optional. Object type. Defaults to empty string.
	 *
	 * @return bool Whether or not the field was updated successfully.
	 */
	public function update_value(
		$value,
		$object,
		string $field_name,
		WP_REST_Request $request = null,
		string $object_type = ''
	): bool {

		if ( $this->permission_callback && ! ( $this->permission_callback )() ) {
			return false;
		}

		if ( empty( $object->id ) ) {
			return false;
		}

		return some_field_updater_callback( $object->id, $field_name, (bool) $value );
	}
}
```

### Example Formatter Implementation

It is a good idea to separate handling a request and preparing the response data.
For this reason, the following code shows a potential formatter, even though it is not actually part of this package.

```php
<?php
use Inpsyde\WPRESTStarter\Common\Endpoint\Schema;
use Inpsyde\WPRESTStarter\Common\Response\DataAccess;
use Inpsyde\WPRESTStarter\Common\Response\DataFilter;
use Inpsyde\WPRESTStarter\Core\Response\LinkAwareDataAccess;
use Inpsyde\WPRESTStarter\Factory\ResponseFactory;

class SomeFormatter {

	/**
	 * @var string
	 */
	private $link_base;

	/**
	 * @var array
	 */
	private $properties;

	/**
	 * @var DataAccess
	 */
	private $response_data_access;

	/**
	 * @var DataFilter
	 */
	private $response_data_filter;

	/**
	 * @var ResponseFactory
	 */
	private $response_factory;

	/**
	 * Constructor. Sets up the properties.
	 *
	 * @param Schema          $schema               Schema object.
	 * @param string          $namespace            Namespace.
	 * @param DataFilter      $response_data_filter Response data filter object.
	 * @param ResponseFactory $response_factory     Optional. Response factory object. Defaults to null.
	 * @param DataAccess      $response_data_access Optional. Response data access object. Defaults to null.
	 */
	public function __construct(
		Schema $schema,
		string $namespace,
		DataFilter $response_data_filter,
		ResponseFactory $response_factory = null,
		DataAccess $response_data_access = null
	) {

		$this->properties = $schema->get_properties();

		$this->link_base = $namespace . '/' . $schema->get_title();

		$this->response_data_filter = $response_data_filter;

		$this->response_factory = $response_factory ?? new ResponseFactory();

		$this->response_data_access = $response_data_access ?? new LinkAwareDataAccess();
	}

	/**
	 * Returns a formatted representation of the given data.
	 *
	 * @param array[] $raw_data Raw data.
	 * @param string  $context  Optional. Request context. Defaults to 'view'.
	 *
	 * @return array The formatted representation of the given data.
	 */
	public function format( array $raw_data, string $context = 'view' ): array {

		$data = array_reduce( $raw_data, function ( array $data, array $set ) use ( $context ) {

			$item = [
				'id'       => (int) ( $set['id'] ?? 0 ),
				'name'     => (string) ( $set['name'] ?? '' ),
				'redirect' => (bool) ( $set['redirect'] ?? false ),
			];
			$item = $this->response_data_filter->filter_data( $item, $context );

			$response = $this->get_response_with_links( $item, $set );

			$data[] = $this->response_data_access->get_data( $response );

			return $data;
		}, [] );

		return $data;
	}

	/**
	 * Returns a response object with the given data and all relevant links.
	 *
	 * @param array $data Response data.
	 * @param array $set  Single data set.
	 *
	 * @return WP_REST_Response The response object with the given data and all relevant links.
	 */
	private function get_response_with_links( array $data, array $set ): WP_REST_Response {

		$links = [];

		if ( isset( $set['id'] ) ) {
			$links['self'] = [
				'href' => rest_url( $this->link_base . '/' . absint( $set['id'] ) ),
			];
		}

		$links['collection'] = [
			'href' => rest_url( $this->link_base ),
		];

		$response = $this->response_factory->create( [ $data ] );
		$response->add_links( $links );

		return $response;
	}
}
```

## License

Copyright (c) 2017 Inpsyde GmbH

This code is licensed under the [MIT License](LICENSE).
