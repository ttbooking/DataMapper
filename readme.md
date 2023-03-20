## Простой мэпер для php ##

* Заполнение объектов в массив.
* Работает на аттрибутах.
* Кастинг простых типов.
* Возможность использовать преобразователи для сложных типов.
* Несколько типов на свойство.

### сценарий использования ###

```php
<?php
use DataMapper\Elements\DataMapper;
/**
 * Описание структуры объекта
 */
class Example extends DataMapper {
    public string $property;
}

...

/**
 * Использование
 */
$item = Example::map(['property' => 'value']);
echo $item->property; // value

/**
 * Массив данных
 */
$items = Example::mapMany([
    ['property' => 'value'],
    ['property' => 'value2']
]);

```
### Встроенные аттрибуты ###
#### ManyOf ####
Нужен когда каждый из элементов массива должен быть приведен к типу.
Принимает простые типы, либо Mappable
По сути выполняет роль Caster, для удобства вынесен в атрибут

```php
use DataMapper\Attributes\ManyOf;
class Example extends DataMapper {
    #[ManyOf('int')]
    public array $values;
}
$item = Example::map(['values' => ['1', 3, '5', '0']]);
/**
 * $item->values = [1, 3, 5, 0];
 */
```
В случае когда в качестве типа указан класс реализующий Mappable, 
управление заполнения передается ему.

#### FromName & ToName ####
Позволяет задать имя свойства при заполнении, а так же имя свойства при сериализации в массив

```php
use DataMapper\Attributes\FromName;
use DataMapper\Attributes\ManyOf;
use DataMapper\Attributes\ToName;

class Example extends DataMapper {
    #[ManyOf('int'), FromName('integers'), ToName('integerValues')]
    public array $values;
}
$item = Example::map(['integers' => ['1', 3, '5', '0']]);
/**
 * $item->values = [1, 3, 5, 0];
 * $item->toArray() = ['integerValues' => [1, 3, 5, 0]]
 */
```
#### IgnoresSerialization ####
Позволяет исключить свойство при сериализации из результирующего массива
В примере не нуждается

#### Caster ####
Аттрибут принимает значение имя класса реализующего InputCaster или 
OutputCaster
##### DateTimeCaster #####
class Example extends DataMapper {
    #[ManyOf('int'), FromName('integers'), ToName('integerValues')]
    public array $values;
}
### Несколько типов (Union types) ###
Приоритет отдается первому типу из установленных, 
однако при заполнении из объекта берется 
тип соответствующий переданному объекту

Создавать аттрибуты вряд ли понадобится, однако такая возможность имеется
Создание преобразователей (Caster) производится реализацией соответствующих 
интерфейсов


InputCaster нужен для преобразования данных в момент заполнения


OutputCaster нужен при преобразовании данных в момент сериализации


### Планы на будущее ###
* Добавить передачу InputCaster в качестве параметра ManyOf 
или подобного рода действия, позволяющие применять преобразование
к каждому элементу