<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests\Unit\Core\Response;

use Inpsyde\WPRESTStarter\Common\Endpoint\Schema;
use Inpsyde\WPRESTStarter\Core\Response\SchemaAwareDataFilter as Testee;
use Inpsyde\WPRESTStarter\Tests\Unit\TestCase;

/**
 * Test case for the schema-aware response data filter class.
 *
 * @package Inpsyde\WPRESTStarter\Tests\Unit\Core\Response
 * @since   2.0.0
 */
class SchemaAwareDataFilterTest extends TestCase {

	/**
	 * Tests filtering the reponse data.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_filter_data() {

		$context = 'test';

		$schema = \Mockery::mock( Schema::class );
		$schema->shouldReceive( 'properties' )
			->andReturn( [
				'no-context'    => [ null ],
				'wrong-context' => [
					'context' => [ "not-$context" ],
				],
				'no-properties' => [
					'context' => [ $context ],
				],
				'no-object'     => [
					'context'    => [ $context ],
					'properties' => [ null ],
				],
				'property'      => [
					'context'    => [ $context ],
					'type'       => 'object',
					'properties' => [
						'no-context'    => [ null ],
						'valid-context' => [
							'context' => [ $context ],
						],
						'wrong-context' => [
							'context' => [ "not-$context" ],
						],
					],
				],
			] );

		$data = [
			'extraneous'    => 'Shows up as is. Key not included in schema properties.',
			'no-context'    => 'Shows up as is. No context specified in schema properties.',
			'wrong-context' => 'Does NOT show up. Passed context not specified in schema properties.',
			'no-properties' => 'Shows up as is. No property properties specified in schema properties.',
			'no-object'     => 'Shows up as is. Property not of type object.',
			'property'      => [
				'not-included'  => 'Shows up as is. Property not included in property properties.',
				'no-context'    => 'Shows up as is. No context specified in property properties.',
				'valid-context' => 'Shows up as is. Passed context specified in property properties.',
				'wrong-context' => 'Does NOT show up. Passed context not specified in property properties.',
			],
		];

		$expected = [
			'extraneous'    => 'Shows up as is. Key not included in schema properties.',
			'no-context'    => 'Shows up as is. No context specified in schema properties.',
			'no-properties' => 'Shows up as is. No property properties specified in schema properties.',
			'no-object'     => 'Shows up as is. Property not of type object.',
			'property'      => [
				'not-included'  => 'Shows up as is. Property not included in property properties.',
				'no-context'    => 'Shows up as is. No context specified in property properties.',
				'valid-context' => 'Shows up as is. Passed context specified in property properties.',
			],
		];

		self::assertSame( $expected, ( new Testee( $schema ) )->filter_data( $data, $context ) );
	}
}
