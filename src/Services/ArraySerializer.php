<?php

namespace DataMapper\Services;

use BackedEnum;

class ArraySerializer
{
    public static function toArray($value)
    {
        if (!is_null($value)) {

            if (!is_object($value) && !is_array($value)) {
                return $value;
            }

            if (is_object($value)) {

                if ($value instanceof \JsonSerializable) {
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
                $newValue = [];
                foreach ($value as $k => $v) {
                    $newValue[$k] = static::toArray($v);
                }
                return $newValue;
            }
        }
        return null;
    }
}