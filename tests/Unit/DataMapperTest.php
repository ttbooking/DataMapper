<?php


namespace Tests\Unit;

use DataMapper\Attributes\Caster;
use DataMapper\Attributes\FromName;
use DataMapper\Attributes\IgnoresSerialization;
use DataMapper\Attributes\ManyOf;
use DataMapper\Attributes\ToName;
use DataMapper\Casters\DateTimeCaster;
use DataMapper\Elements\DataMapper;
use PHPUnit\Framework\TestCase;

class DataMapperTest extends TestCase
{

    public function testMapper()
    {
        $class = $this->getTestClass();
        $data = $this->getTestData();

        $mapped = $class::map($data);
        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), $mapped->toJson(JSON_PRETTY_PRINT));
    }

    public function testMapperObject()
    {
        $class = $this->getTestClass();
        $data = json_decode(json_encode($this->getTestData()));
        $mapped = $class::map($data);
        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), $mapped->toJson(JSON_PRETTY_PRINT));
    }

    public function testMapperMapped()
    {
        $class = $this->getTestClass();
        $data = $this->getTestData();
        $mapped = $class::map($class::map($data));
        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), json_encode($mapped, JSON_PRETTY_PRINT));
    }

    public function testPerformanceMap()
    {
        $class = $this->getTestClass();
        $data = $this->getTestData();

        $time = 0;
        $range = range(1, 10_000);
        foreach ($range as $i) {
            $mark = microtime(true);
            $class::map($data);
            $time += microtime(true) - $mark;
        }
        $this->assertLessThan(.3, $time);
    }

    public function testPerformanceToArray()
    {
        $class = $this->getTestClass();
        $data = $this->getTestData();

        $time = 0;
        $range = range(1, 10_000);
        foreach ($range as $i) {
            $mapped = $class::map($data);
            $mark = microtime(true);
            $mapped->toArray();
            $time += microtime(true) - $mark;
        }
        $this->assertLessThan(.3, $time);
    }

    public function getTestData(): array
    {
        return [
            'fruit' => 'banana',
            'count' => 4,
            'expires' => '2023-07-11',
            'combines' => [
                [
                    'fruit' => 'apple',
                    'count' => 3,
                    "expires" => null,
                    "combines" => [
                        [
                            'fruit' => 'banana',
                            'count' => 4,
                            'expires' => '2023-07-11',
                            'combines' => [],
                        ],
                    ],
                    'someValue' => 'value2',
                ],
            ],
            'someValue' => 'value1',
        ];
    }

    public function getTestClass(): DataMapper
    {

        return new class extends DataMapper
        {
            public string $fruit;
            public int $count;
            #[Caster(DateTimeCaster::class, 'Y-m-d')]
            public \DateTimeInterface $expires;
            #[ManyOf(self::class), FromName('combines'), ToName('combines')]
            public array $combinations = [];
            #[IgnoresSerialization]
            public ?string $some = 'something string';
            #[IgnoresSerialization(IgnoresSerialization::NULL)]
            public ?Some $someValue = null;
        };
    }
}

enum Some: string
{
    case VALUE1 = 'value1';
    case VALUE2 = 'value2';
}