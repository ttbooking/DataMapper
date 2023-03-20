<?php

namespace Tests\Unit\Attributes;

use DataMapper\Attributes\Caster;
use DataMapper\Casters\DateTimeCaster;
use DataMapper\Elements\DataMapper;
use PHPUnit\Framework\TestCase;

class CasterAttributeTest extends TestCase
{

	public function testCast() {

		$date = new \DateTime('2008-05-30 14:34');

		$data = [
			'date' => $date,
			'date2' => $date->format(DATE_ATOM),
			'date3' => $date->getTimestamp(),
			'nullable' => null,
		];

		$class = new class extends DataMapper {
			#[Caster(DateTimeCaster::class, DATE_ATOM)]
			public \DateTimeImmutable|\DateTime $date;
			#[Caster(DateTimeCaster::class, DATE_ATOM)]
			public \DateTimeImmutable $date2;
			#[Caster(DateTimeCaster::class, DATE_ATOM, 'U', 'Europe/Kaliningrad')]
			public \DateTime $date3;
			#[Caster(DateTimeCaster::class, DATE_ATOM)]
			public ?\DateTimeInterface $nullable;
		};

		$mapped = $class::map($data);

		$this->assertNotEmpty($mapped->date);
		$this->assertNotEmpty($mapped->date2);
		$this->assertNotEmpty($mapped->date3);
		$this->assertNull($mapped->nullable);

		$this->assertInstanceOf(\DateTime::class, $mapped->date);
		$this->assertSame($date->format(DATE_ATOM), $mapped->date->format(DATE_ATOM));
		$this->assertSame($date->format(DATE_ATOM), $mapped->date2->format(DATE_ATOM));
		$this->assertSame($date->format(DATE_ATOM), $mapped->date3->format(DATE_ATOM));


	}
}