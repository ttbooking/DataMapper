<?php

namespace Tests\Unit\Attributes;

use DataMapper\Attributes\ManyOf;
use DataMapper\Elements\DataMapper;
use PHPUnit\Framework\TestCase;


class ManyOfAttributeTest extends TestCase
{
    public function testManyOfMappable()
    {

        $data = [
            'collection' => new MyCollection([
                1 => [
                    'some' => 'one',
                ],
                3 => [
                    'some' => 2,
                ],
            ]),
        ];
        $mapped = SomeCollection::map($data);
        $this->assertNotEmpty($mapped->collection);
        foreach ($mapped->collection as $index => $someItem) {
            $this->assertInstanceOf(Some::class, $someItem);
            $this->assertSame($data['collection']->get($index)['some'], $someItem->some);
        }
    }

    public function testManyOfSimple()
    {
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

    public function testEmptyCollection()
    {
        $data = [
            'collection' => [

            ]
        ];
        $mapped = SomeCollection::map($data);
        $this->assertSame([], $mapped->collection);
    }
}
class MyCollection implements \Iterator {

    private int $index = 0;

    public function __construct(
        public array $data = []
    ) {}

    public function get(int|string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function current(): mixed
    {
        return array_values($this->data)[$this->index];
    }

    public function next(): void
    {
        $this->index++;
    }

    public function key(): mixed
    {
        return array_keys($this->data)[$this->index];
    }

    public function valid(): bool
    {
        return count($this->data) > $this->index;
    }

    public function rewind(): void
    {
        $this->index = 0;
    }
}

class SomeCollection extends DataMapper
{
    #[ManyOf(Some::class)]
    public array|MyCollection $collection;
}

class Some extends DataMapper
{
    public string|int $some;
}

/**
 *
 */
class SimpleManyOf extends DataMapper
{
    #[ManyOf('int')]
    public array $intArray;
    #[ManyOf('string')]
    public array $stringArray;
    #[ManyOf('bool')]
    public array $boolArray;
}