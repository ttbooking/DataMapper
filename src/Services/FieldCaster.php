<?php

namespace DataMapper\Services;

use BackedEnum;
use DataMapper\Elements\ObjectInfo\PropertyInfo;
use DataMapper\Interfaces\Mappable;
use Throwable;

class FieldCaster
{
	public static function castValue(PropertyInfo $propertyInfo, mixed $value): mixed
	{
		$valueType = gettype($value);
		$valueSimpleType = SimpleTypeHelper::isSimpleType($valueType);
		if (in_array($valueType, $propertyInfo->propertyTypes)) {
			/**
			 * Кастинг не нужен, если тип уже подходит под тип поля
			 */
			return $value;
		} else {

			/**
			 * В первую очередь смотрим аналогичный тип
			 */
			foreach ($propertyInfo->propertyTypes as $propertyType) {
				if ($value instanceof $propertyType) {
					return $value;
				}
			}

			/**
			 * Во вторую очередь смотрим на тип и пытаемся угадать, что нужно сделать
			 */
			foreach ($propertyInfo->propertyTypes as $propertyType) {

				if (SimpleTypeHelper::isSimpleType($propertyType) && $valueSimpleType) {
					/**
					 * В случае если тип значения простой и тип поля простой, то однозначно можем установить
					 */
					return SimpleTypeHelper::castSimpleType($propertyType, $value);
				} else if (is_a($propertyType, BackedEnum::class, true)) {
					/**
					 * Пробуем получить enum из значения
					 */
					$result = $propertyType::tryFrom($value);
					if ($result) {
						return $result;
					}
				} else if ($propertyType === 'array') {
					/**
					 * Если тип свойства массив, то преобразуем к массиву
					 */
					return ArraySerializer::toArray($value);
				} else if (is_a($propertyType, Mappable::class, true) && !$valueSimpleType) {
					/**
					 * О так это Mappable, передадим значение туда
					 * @thinkabout преобразовать к массиву?
					 */
					return $propertyType::map($value);
				}
			}

			/**
			 * В последнюю очередь пробуем получить экземпляр типа, передав значение в конструктор
			 */
			foreach ($propertyInfo->propertyTypes as $type) {
				if (class_exists($type)) {
					try {
						return new $type($value);
					} catch (Throwable) {
						continue;
					}
				}
			}
		}

		/**
		 * Простите, мы сделали все, что смогли
		 */
		return null;
	}
}