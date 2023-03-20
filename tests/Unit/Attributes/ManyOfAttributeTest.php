<?php

namespace Tests\Unit\Attributes;

use DataMapper\Attributes\ManyOf;
use DataMapper\Elements\DataMapper;
use PHPUnit\Framework\TestCase;


class ManyOfAttributeTest extends TestCase
{
	public function testManyOfMappable() {

		$data = [
			'collection' => [
				[
					'some' => 'one',
				]
			],
		];
		$mapped = SomeCollection::map($data);
		$this->assertNotEmpty($mapped->collection);
		foreach ($mapped->collection as $someItem) {
			$this->assertInstanceOf(Some::class, $someItem);
			$this->assertSame('one', $someItem->some);
		}
	}

	public function testManyOfSimple() {
		$data = [
			'intArray' => ['1', '3', '5'],
			'stringArray' => [2, 4, 6],
			'boolArray' => [0, 1, 0]
		];
		$mapped = SimpleManyOf::map($data);
		$this->assertSame([1, 3, 5], $mapped->intArray);
		$this->assertSame(['2', '4', '6'], $mapped->stringArray);
		$this->assertSame([false, true, false], $mapped->boolArray);
	}

	public function testEmptyCollection() {
		$data = [
			'collection' => []
		];
		$mapped = SomeCollection::map($data);
		$this->assertSame([], $mapped->collection);
	}
}

class SomeCollection extends DataMapper {
	#[ManyOf(Some::class)]
	public array $collection;
}
class Some extends DataMapper {
	public string $some;
}

/**
 *
 */
class SimpleManyOf extends DataMapper {
	#[ManyOf('int')]
	public array $intArray;
	#[ManyOf('string')]
	public array $stringArray;
	#[ManyOf('bool')]
	public array $boolArray;
}