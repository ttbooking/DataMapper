<?php

namespace Tests\Unit\Attributes;

use DataMapper\Attributes\IgnoresSerialization;
use DataMapper\Elements\DataMapper;
use PHPUnit\Framework\TestCase;

class IgnoresSerializationAttributeTest extends TestCase
{
	public function testIgnoreSerialization() {
		$data = [
			'some' => 'one',
		];
		$class = new class extends DataMapper {
			#[IgnoresSerialization]
			public string $some;
		};
		$mapped = $class::map($data);
		$this->assertSame($data['some'], $mapped->some);
		$this->assertEmpty($mapped->toArray());
	}
}