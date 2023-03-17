<?php

namespace DataMapper\Services;

use DataMapper\Elements\ObjectInfo\PropertyInfo;
use DataMapper\Interfaces\AttributeAction;
use ReflectionAttribute;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

class PropertiesInfoProvider
{
	/**
	 * @param object $instance
	 * @return array<array-key, PropertyInfo>
	 */
	public static function getProperties(object $instance): array
	{
		static $cache = [];

		if (empty($cache[$instance::class])) {
			$result = [];
			$reflectionClass = new \ReflectionClass($instance);
			$properties = $reflectionClass->getProperties();
			foreach ($properties as $property) {
				if ($property->isPublic()) {
					$result[$property->name] = static::getPropertyInfo($property);
				}
			}

			$cache[$instance::class] = $result;
		}

		return $cache[$instance::class];
	}

	protected static function getPropertyInfo(ReflectionProperty $property): PropertyInfo
	{
		$propertyInfo = new PropertyInfo();

		$propertyInfo->attributeActions = static::getPropertyAttributeActions($property);
		foreach ($propertyInfo->attributeActions as $action) {
			$action->setPropertyInfo($propertyInfo);
		}

		$propertyType = $property->getType();
		$propertyInfo->nullable = $propertyType->allowsNull();
		$propertyInfo->propertyTypes = static::getPropertyTypes($propertyType);
		$propertyInfo->name = $property->getName();

		return $propertyInfo;
	}

	protected static function getPropertyAttributeActions(ReflectionProperty $property): array
	{
		return array_map(
			fn(ReflectionAttribute $attribute) => $attribute->newInstance(),
			$property->getAttributes(AttributeAction::class, ReflectionAttribute::IS_INSTANCEOF)
		);
	}

	protected static function getPropertyTypes(ReflectionType $property): array
	{
		if ($property instanceof ReflectionUnionType) {

			$types = array_map(
				fn(ReflectionNamedType $prop) => $prop->getName(),
				$property->getTypes()
			);
			return array_filter($types, fn(string $name) => $name !== 'null');
		} else if ($property instanceof ReflectionIntersectionType) {

			return $property->getTypes();
		} else if ($property instanceof ReflectionNamedType) {

			return [$property->getName()];
		}
		return [];
	}
}