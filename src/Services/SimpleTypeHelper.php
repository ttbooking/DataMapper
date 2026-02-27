<?php

namespace DataMapper\Services;

class SimpleTypeHelper
{
    const REAL_SIMPLE_TYPE = [
        'boolean' => 'bool',
        'integer' => 'int',
    ];

    const ALLOW_SIMPLE_TYPE = [
        'bool',
        'int',
        'float',
        'double',
        'string',
        'boolean',
        'integer',
    ];

    public static function isSimpleType(string $type): bool
    {
        return in_array($type, self::ALLOW_SIMPLE_TYPE);
    }


    public static function castSimpleType(string $type, mixed $value): bool|int|float|string|null
    {
        return match ($type) {
            'bool', 'boolean' => (bool) $value,
            'integer', 'int' => (int) $value,
            'float' => (float) $value,
            'double' => (double) $value,
            'string' => (string) $value,
        };
    }

    public static function getTrueType(mixed $value): string
    {
        $result = gettype($value);
        return self::REAL_SIMPLE_TYPE[$result] ?? $result;
    }

}