<?php

namespace DataMapper\Interfaces;

use DataMapper\Elements\ObjectInfo\PropertyInfo;

interface OutputCaster
{
    public function castOutput(PropertyInfo $propertyInfo, mixed $value): mixed;
}