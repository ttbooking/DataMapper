<?php

namespace Tests\Unit\Attributes;

use DataMapper\Attributes\FromName;
use DataMapper\Attributes\ToName;
use DataMapper\Elements\DataMapper;
use PHPUnit\Framework\TestCase;

class FromToNameAttributeTest extends TestCase
{

    public function testFromToName()
    {
        $data = [
            'somethingString' => 'hello',
        ];

        $class = new class extends DataMapper
        {
            #[FromName('somethingString'), ToName('somethingString')]
            public string $string;
        };

        $mapped = $class::map($data);
        $this->assertSame($data['somethingString'], $mapped->string);
        $this->assertSame($data, $mapped->toArray());
    }


}