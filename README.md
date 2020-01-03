# Клиент для сервиса географии

Набор классов для взаимодействия с микросервисом географии.

## Требования
PHP 7.2+
Расширения: curl, json

## Установка
```
composer require yurcrm/geo-client
```

## Использование

### Получение информации о городе по его id
```php
$geoClient = new GeoClient(URL_сервиса);
$town = $geoClient->getTownById(id_города, params);
```
Результатом будет объект класса GeoServiceClient\models\Town или выброшено исключение GeoServiceClient\exceptions\NotFoundException, если город не найден

### Получение информации о регионе по его id
```php
$geoClient = new GeoClient(URL_сервиса);
$region = $geoClient->getRegionById(id_региона, params);
```
* params - дополнительные параметры запроса (см. ниже)

Результатом будет объект класса GeoServiceClient\models\Region или выброшено исключение GeoServiceClient\exceptions\NotFoundException, если регион не найден

### Получение информации о городах, ближайших к заданному
```php
$geoClient = new GeoClient(URL_сервиса);
// $townId - id города, для которого хотим получить соседей
$region = $geoClient->getClosestTowns($townId, $radius = 100, $limit = 10);
```
Результатом будет массив объектов класса GeoServiceClient\models\Town

### Получение информации о городах по списку id
```php
$geoClient = new GeoClient(URL_сервиса);
$region = $geoClient->getTownsByIds([1,2,56,777], params);
```
Результатом будет массив объектов класса GeoServiceClient\models\Town

### Получение информации о городах по критериям поиска
```php
$geoClient = new GeoClient(URL_сервиса);
$region = $geoClient->getTowns(limit, regionId, countryId, search, params);
```
Все параметры являются необязательными
* limit - лимит выборки (по умолчанию 10)
* regionId - id региона
* countryId - id страны (по умолчанию 2 - Россия)
* search - строка для поиска по названию города
* params - дополнительные параметры запроса (см. ниже)

Результатом будет массив объектов класса GeoServiceClient\models\Town

### Получение информации о регионах по критериям поиска
```php
$geoClient = new GeoClient(URL_сервиса);
$region = $geoClient->getTowns(limit, countryId, params);
```
Все параметры являются необязательными
* limit - лимит выборки (по умолчанию 10)
* countryId - id страны (по умолчанию 2 - Россия)
* params - дополнительные параметры запроса (см. ниже)

Результатом будет массив объектов класса GeoServiceClient\models\Region

### Получение связанных сущностей
При выборках городов и регионов доступен режим выборки вместе со связанными сущностями (регионы, страны и страны соответственно).
Задайте в аргументе $params ['with' => 'region,country'] для города или ['with' => 'country'] для региона.

## Запуск тестов
Простой запуск
```
vendor/bin/phpunit tests
```

Запуск с анализом покрытия (требует Xdebug)
```
vendor/bin/phpunit tests --coverage-html tests/output/coverage
```