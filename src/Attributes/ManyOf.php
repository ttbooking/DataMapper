<?php

namespace DataMapper\Attributes;

use Attribute;
use BackedEnum;
use DataMapper\Elements\ObjectInfo\PropertyInfo;
use DataMapper\Exceptions\CannotCastException;
use DataMapper\Interfaces\AttributeAction;
use DataMapper\Interfaces\Mappable;
use DataMapper\Services\ArraySerializer;
use DataMapper\Services\SimpleTypeHelper;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ManyOf implements AttributeAction
{
    public function __construct(
        /** @var class-string<Mappable>|string */
        public string $type,
    ) {}

    public function setPropertyInfo(PropertyInfo $propertyInfo): void {}

    /**
     * @throws CannotCastException
     */
    public function handle(mixed $value): mixed
    {
        if (SimpleTypeHelper::isSimpleType($this->type)) {
            $result = [];
            foreach ($value as $k => $item) {
                $result[$k] = SimpleTypeHelper::castSimpleType($this->type, $item);
            }
            return $result;
        } elseif (is_a($this->type, BackedEnum::class, true)) {
            $result = [];
            foreach ($value as $k => $item) {
                $result[$k] = $this->type::tryFrom($item);
            }
            return $result;
        } elseif (is_a($this->type, Mappable::class, true)) {
            return $this->type::mapMany(ArraySerializer::toArray($value));
        }

        throw new CannotCastException(
            'expected simple type or class name implements Mappable, but got ' . $this->type
        );

    }
}