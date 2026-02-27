<?php

namespace DataMapper\Services;

use BackedEnum;
use JsonSerializable;

class ArraySerializer
{
    public static function toArray(mixed $value)
    {
        if (!is_null($value)) {

            if (!is_object($value) && !is_array($value)) {
                return $value;
            }

            if (is_object($value)) {
                if (is_iterable($value)) {
                    $value = iterator_to_array($value);
                } elseif ($value instanceof JsonSerializable) {
                    return $value->jsonSerialize();
                } elseif (method_exists($value, 'toArray')) {
                    return $value->toArray();
                } elseif ($value instanceof BackedEnum) {
                    return $value->value;
                } else {
                    $value = (array) $value;
                }
            }

            if (is_array($value)) {
                return array_map(
                    static fn(mixed $v) => static::toArray($v),
                    $value
                );
            }
        }
        return null;
    }
}