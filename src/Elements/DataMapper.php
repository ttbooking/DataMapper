<?php

namespace DataMapper\Elements;

use DataMapper\Exceptions\UnsupportedDataException;
use DataMapper\Interfaces\Mappable;
use DataMapper\Services\ArraySerializer;
use DataMapper\Services\PropertiesInfoProvider;
use stdClass;

abstract class DataMapper implements Mappable, \JsonSerializable
{

	public function __construct() {}

	/**
	 * @throws UnsupportedDataException
	 */
	public static function map(mixed $value): static
	{
		if ($value instanceof stdClass) {
			$value = (array)$value;
		} else if (is_object($value)) {
			$value = ArraySerializer::toArray($value);
		}

		if (!is_array($value)) {
			throw new UnsupportedDataException('provided type is not supported for map');
		}

		$instance = new static();

		$propertyInfos = PropertiesInfoProvider::getProperties($instance);

		foreach ($propertyInfos as $propertyInfo) {
			$mapFromName = $propertyInfo->serializationInfo->mapFromName ?: $propertyInfo->name;
			if (isset($value[$mapFromName])) {
				$propertyValue = $value[$mapFromName];
				foreach ($propertyInfo->attributeActions as $action) {
					$propertyValue = $action->handle($propertyValue);
				}

				foreach ($propertyInfo->serializationInfo->inputCasters as $caster) {
					$propertyValue = $caster->castInput($propertyInfo, $propertyValue);
				}

				$propertyValueType = gettype($propertyValue);
				if (in_array($propertyValueType, $propertyInfo->propertyTypes)) {
					$instance->{$propertyInfo->name} = $propertyValue;
				} else {
					$filled = false;
					foreach ($propertyInfo->propertyTypes as $propertyType) {
						if (in_array($propertyValueType, [
							'boolean', 'bool',
							'integer', 'int',
							'float', 'double',
							'string'
						])) {
							$instance->{$propertyInfo->name} = $propertyValue;
							$filled = true;
							break;
						} else if ($propertyType === 'array') {
							$instance->{$propertyInfo->name} = (array)$propertyValue;
							$filled = true;
							break;
						} else if (is_a($propertyType, Mappable::class, true)) {
							$instance->{$propertyInfo->name} = $propertyType::map($propertyValue);
							$filled = true;
							break;
						} else if ($propertyValue instanceof $propertyType) {
							$instance->{$propertyInfo->name} = $propertyValue;
							$filled = true;
							break;
						}
					}
					if (!$filled && class_exists($propertyValueType)) {
						$instance->{$propertyType} = new $propertyValueType($propertyValue);
					}
				}
			}
		}

		return $instance;
	}

	/**
	 * @throws UnsupportedDataException
	 */
	public static function mapMany(iterable $values): array
	{
		$result = [];
		foreach ($values as $value) {
			$result[] = static::map($value);
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
			if ($propertyInfo->serializationInfo->ignoresSerialization) {
				continue;
			}
			$propertySerializeName = $propertyInfo->serializationInfo->serializeToName ?? $propertyInfo->name;
			$value = $this->{$propertyInfo->name} ?? null;


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