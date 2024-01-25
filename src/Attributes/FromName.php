<?php

namespace DataMapper\Attributes;

use Attribute;
use DataMapper\Elements\ObjectInfo\PropertyInfo;
use DataMapper\Interfaces\AttributeAction;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FromName implements AttributeAction
{
    public function __construct(
        public string $fromName,
    ) {}

    public function setPropertyInfo(PropertyInfo $propertyInfo): void
    {
        $propertyInfo->serializationInfo->mapFromName = $this->fromName;
    }

    public function handle(mixed $value): mixed
    {
        return $value;
    }

}