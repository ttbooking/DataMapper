<?php

namespace DataMapper\Attributes;

use Attribute;
use DataMapper\Elements\ObjectInfo\PropertyInfo;
use DataMapper\Interfaces\AttributeAction;
use DataMapper\Interfaces\Mappable;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ManyOf implements AttributeAction
{
	public function __construct(
		/** @var class-string<Mappable>|Mappable */
		public string $className,
	) {}

	public function setPropertyInfo(PropertyInfo $propertyInfo): void {}

	public function handle(mixed $value): mixed
	{
		return $this->className::mapMany($value);
	}
}