<?php

namespace Tests\Fixtures\TestData1;

use DataMapper\Attributes\Caster;
use DataMapper\Attributes\FromName;
use DataMapper\Attributes\IgnoresSerialization;
use DataMapper\Attributes\ToName;
use DataMapper\Casters\DateTimeCaster;
use DataMapper\Elements\DataMapper;

class SubItem extends DataMapper
{
	public ?string $propertyString = null;
	public ?int $propertyInt = null;
	#[IgnoresSerialization]
	public ?array $propertyArray = null;
	#[FromName('propertyDate'), ToName('propertyDate'), Caster(DateTimeCaster::class, 'd.m.Y H:i')]
	public ?\DateTimeInterface $date = null;
}