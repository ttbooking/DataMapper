<?php

namespace Tests\Fixtures\TestData1;

use DataMapper\Attributes\FromName;
use DataMapper\Attributes\ManyOf;
use DataMapper\Attributes\ToName;
use DataMapper\Elements\DataMapper;

class Item extends DataMapper
{
	#[FromName('propertyString'), ToName('propertyString')]
	public ?string $string = null;
	#[FromName('propertyInt'), ToName('propertyInt')]
	public ?int $int = null;
	#[FromName('propertyArray'), ToName('propertyArray')]
	public ?array $array = null;
	#[FromName('propertyItem'), ToName('propertyItem')]
	public SubItem|null|DataMapper $subItem = null;
	#[ManyOf(SubItem::class), FromName('propertyArrayObjects'), ToName('propertyArrayObjects')]
	public ?array $arrayObjects = null;
}