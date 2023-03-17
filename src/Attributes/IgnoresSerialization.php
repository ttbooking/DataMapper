<?php

namespace DataMapper\Attributes;

use Attribute;
use DataMapper\Elements\ObjectInfo\PropertyInfo;
use DataMapper\Interfaces\AttributeAction;

#[Attribute(Attribute::TARGET_PROPERTY)]
class IgnoresSerialization implements AttributeAction
{

	public function setPropertyInfo(PropertyInfo $propertyInfo): void
	{
		$propertyInfo->serializationInfo->ignoresSerialization = true;
	}

	public function handle(mixed $value): mixed
	{
		return $value;
	}

}