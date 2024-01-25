<?php

namespace Tests\Unit\Attributes;

use DataMapper\Attributes\IgnoresSerialization;
use DataMapper\Elements\DataMapper;
use PHPUnit\Framework\TestCase;

class IgnoresSerializationAttributeTest extends TestCase
{
    public function testIgnoreSerialization()
    {
        $data = [
            'some' => 'one',
            'some2' => null,
            'some3' => 0,
        ];
        $class = new class extends DataMapper
        {
            #[IgnoresSerialization]
            public string $some;
            #[IgnoresSerialization(IgnoresSerialization::NULL)]
            public ?string $some2;
            #[IgnoresSerialization(IgnoresSerialization::EMPTY)]
            public ?int $some3;
        };
        $mapped = $class::map($data);
        $this->assertSame($data['some'], $mapped->some);
        $this->assertSame($data['some2'], $mapped->some2);
        $this->assertSame($data['some3'], $mapped->some3);
        $this->assertEmpty($mapped->toArray());
    }

    public function testIgnoreSerializationClass()
    {
        $data = [
            'some' => 'one',
            'some2' => null,
            'some3' => 0,
        ];
        $class = new
        #[IgnoresSerialization(IgnoresSerialization::EMPTY)]
        class extends DataMapper
        {
            public string $some;
            public ?string $some2;
            public ?int $some3;
        };

        $mapped = $class::map($data);
        $this->assertSame($data['some'], $mapped->some);
        $this->assertSame($data['some2'], $mapped->some2);
        $this->assertSame($data['some3'], $mapped->some3);
        $this->assertCount(1, $mapped->toArray());
    }
}