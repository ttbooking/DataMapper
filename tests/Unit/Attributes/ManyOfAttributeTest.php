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
                    'some' => Foo::map(['foo' => Bam::map(['bam' => 'one'])]),
                ],
                3 => [
                    'some' => Foo::map(['foo' => Baz::map(['baz' => 2])]),
                ],
                5 => [
                    'some' => Bar::map(['bar' => Baz::map(['baz' => 'some'])]),
                ],
                7 => [
                    'some' => Bar::map(['bar' => Bam::map(['bam' => 4])]),
                ],
            ]),
        ];
        $mapped = SomeCollection::map($data);
        $this->assertNotEmpty($mapped->collection);
        foreach ($mapped->collection as $index => $someItem) {
            $this->assertInstanceOf(Some::class, $someItem);
            $item = $data['collection']->get($index)['some'];
            $this->assertInstanceOf($item ? $item::class : null, $someItem->some);

            $this->assertNotSame($item, $someItem->some);
            if ($item instanceof Foo) {
                $this->assertInstanceOf($item->foo::class, $someItem->some->foo);
                $this->assertNotSame($item->foo, $someItem->some->foo);
            } elseif ($item instanceof Bar) {
                $this->assertInstanceOf($item->bar::class, $someItem->some->bar);
                $this->assertNotSame($item->bar, $someItem->some->bar);
            }
        }
        $resultToArray = $mapped->toArray();
        $this->assertIsArray($resultToArray);
        foreach ($resultToArray['collection'] as $item) {
            $this->assertIsArray($item);
            $this->assertIsArray($item['some']);
            if (isset($item['some']['bar'])) {
                $this->assertIsArray($item['some']['bar']);
            } elseif (isset($item['some']['foo'])) {
                $this->assertIsArray($item['some']['foo']);
            }
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
    public Foo|Bar $some;
}

class Foo extends DataMapper
{
    public Bam|Baz $foo;
}
class Bar extends DataMapper
{
    public Bam|Baz $bar;
}
class Baz extends DataMapper
{
    public string|int $baz;
}
class Bam extends DataMapper
{
    public string|int $bam;
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