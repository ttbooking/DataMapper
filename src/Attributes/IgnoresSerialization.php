<?php

namespace DataMapper\Attributes;

use Attribute;
use DataMapper\Elements\ObjectInfo\PropertyInfo;
use DataMapper\Interfaces\AttributeAction;

#[Attribute(Attribute::TARGET_PROPERTY)]
class IgnoresSerialization implements AttributeAction
{
	const ANY = 'any';
	const NULL = 'null';
	const EMPTY = 'empty';
	public function __construct(public string $case = self::ANY) {}

	public function setPropertyInfo(PropertyInfo $propertyInfo): void
	{
		$propertyInfo->serializationInfo->ignoresSerialization = true;
		$propertyInfo->serializationInfo->ignoreIf = $this->case;
	}

	public function handle(mixed $value): mixed
	{
		return $value;
	}

}