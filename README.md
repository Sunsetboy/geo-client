# Клиент для сервиса географии

Набор классов для взаимодействия с микросервисом географии.

## Требования
PHP 7.2+
Расширения: curl, json

## Запуск тестов
Простой запуск
```
vendor/bin/phpunit tests
```

Запуск с анализом покрытия (требует Xdebug)
```
vendor/bin/phpunit tests --coverage-html tests/output/coverage
```