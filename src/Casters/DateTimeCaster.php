<?php

namespace DataMapper\Casters;

use DataMapper\Elements\ObjectInfo\PropertyInfo;
use DataMapper\Exceptions\CannotCastException;
use DataMapper\Interfaces\InputCaster;
use DataMapper\Interfaces\OutputCaster;
use DateTime;
use DateTimeInterface;
use Throwable;

class DateTimeCaster implements InputCaster, OutputCaster
{

    public function __construct(
        public string $serializeFormat,
        public ?string $inputFormat = null,
        public ?string $tz = null,
    ) {}

    /**
     * @throws CannotCastException
     */
    public function castInput(PropertyInfo $propertyInfo, mixed $value): ?DateTimeInterface
    {
        $className = null;

        if (is_null($value)) {
            return null;
        }

        if (is_object($value)) {
            foreach ($propertyInfo->propertyTypes as $propertyType) {
                if ($value instanceof $propertyType) {
                    return $value;
                }
            }

            foreach ($propertyInfo->propertyTypes as $propertyType) {
                try {
                    return new $propertyType($value);
                } catch (Throwable) {
                    continue;
                }
            }

            throw new CannotCastException(
                'expected ' . implode('|', $propertyInfo->propertyTypes) . ', but got ' . $value::class
            );

        } else {
            foreach ($propertyInfo->propertyTypes as $propertyType) {
                if (is_a($propertyType, DateTimeInterface::class, true)) {
                    $className = $propertyType === DateTimeInterface::class ? DateTime::class : $propertyType;
                    break;
                }
            }
        }

        if (is_null($className)) {
            throw new CannotCastException(
                'expected DateTimeInterface, but got ' . implode('|', $propertyInfo->propertyTypes)
            );
        }

        if ($value instanceof $className) {
            return clone $value;
        }

        $dateTime = null;
        $tz = $this->tz ? new \DateTimeZone($this->tz) : null;

        if ($this->inputFormat) {
            try {
                $dateTime = $className::createFromFormat($this->inputFormat, $value);
            } catch (Throwable) {
            }
        }

        if (!$dateTime) {
            $dateTime = new $className($value);
        }

        if ($dateTime && $tz) {
            $dateTime = $dateTime->setTimezone($tz);
        }

        return $dateTime;
    }

    public function castOutput(PropertyInfo $propertyInfo, mixed $value): ?string
    {
        return $value?->format($this->serializeFormat);
    }
}