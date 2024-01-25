<?php

namespace DataMapper\Attributes;

use Attribute;
use DataMapper\Elements\ObjectInfo\PropertyInfo;
use DataMapper\Interfaces\AttributeAction;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ToName implements AttributeAction
{
    public function __construct(
        public string $toName
    ) {}

    public function setPropertyInfo(PropertyInfo $propertyInfo): void
    {
        $propertyInfo->serializationInfo->serializeToName = $this->toName;
    }

    public function handle(mixed $value): mixed
    {
        return $value;
    }

}