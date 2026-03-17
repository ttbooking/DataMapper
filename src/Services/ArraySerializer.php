<?php

namespace DataMapper\Services;

use BackedEnum;
use JsonSerializable;

class ArraySerializer
{

    public static function toArray($value)
    {
        if (!is_null($value)) {

            if (!is_object($value) && !is_array($value)) {
                return $value;
            }

            if (is_object($value)) {

                if ($value instanceof JsonSerializable) {
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
                return array_map(function ($v) {
                    return static::toArray($v);
                }, $value);
            }
        }
        return null;
    }

    public static function toIterableArray(mixed $value)
    {
        if (is_object($value) || is_array($value)) {
            if (is_iterable($value)) {
                return iterator_to_array($value);
            }
            return (array) $value;
        }
        return $value;
    }
}