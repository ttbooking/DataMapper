<?php

namespace DataMapper\Interfaces;

use DataMapper\Elements\ObjectInfo\PropertyInfo;

interface InputCaster
{
	public function castInput(PropertyInfo $propertyInfo, mixed $value): mixed;
}