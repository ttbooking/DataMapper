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
			throw new UnsupportedDataException('Переданное значение не может быть обработано');
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

					/**
					 * В первую очередь смотрим аналогичный тип и тогда устанавливаем значение
					 */
					foreach ($propertyInfo->propertyTypes as $propertyType) {
						if ($propertyValue instanceof $propertyType) {
							$instance->{$propertyInfo->name} = $propertyValue;
							$filled = true;
							break;
						}
					}

					/**
					 * Во вторую очередь смотрим на тип и пытаемся угадать, что нужно сделать
					 */
					if (!$filled) {
						$simpleTypes = ['boolean', 'bool', 'integer', 'int', 'float', 'double', 'string'];
						foreach ($propertyInfo->propertyTypes as $propertyType) {
							/**
							 * В случае если тип значения простой и тип поля простой, то однозначно можем установить
							 */
							if (in_array($propertyValueType, $simpleTypes) && in_array($propertyType, $simpleTypes)) {
								$instance->{$propertyInfo->name} = $propertyValue;
								$filled = true;
								break;
							} else if ($propertyType === 'array') {
								/**
								 * Если тип свойства массив, то установим преобразовав к массиву
								 */
								$instance->{$propertyInfo->name} = ArraySerializer::toArray($propertyValue);
								$filled = true;
								break;
							} else if (is_a($propertyType, Mappable::class, true)) {
								/**
								 * Если тип наследник, то делегируем преобразование в него
								 */
								$instance->{$propertyInfo->name} = $propertyType::map($propertyValue);
								$filled = true;
								break;
							}
						}
					}

					/**
					 * В последнюю очередь пробуем получить экземпляр типа, передав значение в конструктор
					 */
					if (!$filled) {
						foreach ($propertyInfo->propertyTypes as $type) {
							if (class_exists($type)) {
								try {
									$instance->{$propertyInfo->name} = new $type($propertyValue);
								} catch (\Exception) {
									continue;
								}
							}
						}
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