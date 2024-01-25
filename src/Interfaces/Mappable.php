<?php

namespace DataMapper\Interfaces;

interface Mappable
{
    public function __construct();

    public static function map(mixed $value): Mappable;

    /**
     * @param  iterable  $values
     * @return list<Mappable>
     */
    public static function mapMany(iterable $values): array;
}