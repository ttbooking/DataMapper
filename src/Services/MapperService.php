<?php

namespace DataMapper\Services;

use DataMapper\Interfaces\Mappable;

class MapperService
{
	/**
	 * @template T of Mappable
	 * @param T $object
	 * @param array $value
	 * @return T
	 */
	public static function mapInto(Mappable $object, array $value): Mappable
	{
		$propertyInfos = PropertiesInfoProvider::getProperties($object);

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

				$object->{$propertyInfo->name} = FieldCaster::castValue($propertyInfo, $propertyValue);

			} else {
				if ($propertyInfo->nullable) {
					$object->{$propertyInfo->name} = null;
				}
			}
		}

		return $object;
	}
}