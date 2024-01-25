<?php

namespace DataMapper\Attributes;

use Attribute;
use DataMapper\Elements\ObjectInfo\PropertyInfo;
use DataMapper\Interfaces\AttributeAction;
use DataMapper\Interfaces\InputCaster;
use DataMapper\Interfaces\OutputCaster;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Caster implements AttributeAction
{
    protected array $parameters = [];

    public function __construct(
        /**
         * @var class-string<InputCaster|OutputCaster>
         */
        public string $casterClass,
        ...$parameters,
    ) {
        $this->parameters = $parameters;
    }

    public function setPropertyInfo(PropertyInfo $propertyInfo): void
    {
        $caster = new $this->casterClass(...$this->parameters);

        if (is_a($caster, InputCaster::class)) {
            $propertyInfo->serializationInfo->inputCasters[] = $caster;
        }

        if (is_a($caster, OutputCaster::class)) {
            $propertyInfo->serializationInfo->outputCasters[] = $caster;
        }
    }

    public function handle(mixed $value): mixed
    {
        return $value;
    }

}