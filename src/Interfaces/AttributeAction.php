<?php

namespace DataMapper\Interfaces;

use DataMapper\Elements\ObjectInfo\PropertyInfo;

interface AttributeAction
{
    /**
     * Вызывается в момент получения информации о свойстве, для изменения или дальнейшего использования информации
     * @param  PropertyInfo  $propertyInfo
     * @return void
     */
    public function setPropertyInfo(PropertyInfo $propertyInfo): void;

    /**
     * Вызывается перед установкой значения свойства (обновляет/не трогает/преобразует/ значение)
     * @param  mixed  $value
     * @return mixed
     */
    public function handle(mixed $value): mixed;
}