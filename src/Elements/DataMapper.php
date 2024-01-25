<?php

namespace DataMapper\Elements;

use DataMapper\Attributes\IgnoresSerialization;
use DataMapper\Exceptions\UnsupportedDataException;
use DataMapper\Interfaces\Mappable;
use DataMapper\Services\ArraySerializer;
use DataMapper\Services\MapperService;
use DataMapper\Services\PropertiesInfoProvider;
use JsonSerializable;
use stdClass;

abstract class DataMapper implements Mappable, JsonSerializable
{

    public function __construct() {}

    /**
     * @throws UnsupportedDataException
     */
    public static function map(mixed $value): static
    {
        if ($value instanceof static) {
            return clone $value;
        }

        if ($value instanceof stdClass) {
            $value = (array) $value;
        } elseif (is_object($value)) {
            $value = ArraySerializer::toArray($value);
        }

        if (!is_array($value)) {
            throw new UnsupportedDataException('Переданное значение не может быть обработано');
            //may be need to assign empty array instead of exception
        }

        return MapperService::mapInto(new static(), $value);
    }

    /**
     * @throws UnsupportedDataException
     */
    public static function mapMany(iterable $values): array
    {
        $result = [];
        foreach ($values as $key => $value) {
            $result[$key] = static::map($value);
        }
        return $result;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        $propertyInfos = PropertiesInfoProvider::getProperties($this);

        $result = [];
        foreach ($propertyInfos as $propertyInfo) {

            $value = $this->{$propertyInfo->name} ?? null;

            if ($propertyInfo->serializationInfo->ignoresSerialization) {

                $ignore = match ($propertyInfo->serializationInfo->ignoreIf) {
                    IgnoresSerialization::ANY => true,
                    IgnoresSerialization::NULL => is_null($value),
                    IgnoresSerialization::EMPTY => empty($value),
                };

                if ($ignore) {
                    continue;
                }
            }

            $propertySerializeName = $propertyInfo->serializationInfo->serializeToName ?? $propertyInfo->name;


            foreach ($propertyInfo->serializationInfo->outputCasters as $caster) {
                $value = $caster->castOutput($propertyInfo, $value);
            }

            $result[$propertySerializeName] = ArraySerializer::toArray($value);
        }
        return $result;
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}