<?php

namespace DataMapper\Elements\ObjectInfo;

use DataMapper\Interfaces\AttributeAction;

class PropertyInfo
{
    public string $name;
    /** @var list<class-string> */
    public array $propertyTypes;
    public bool $nullable;

    /** @var list<AttributeAction> */
    public array $attributeActions = [];
    public SerializationInfo $serializationInfo;

    public function __construct()
    {
        $this->serializationInfo = new SerializationInfo();
    }


}