# API Response Interface для MyGenetics

Пакет предназначен для создания единообразия интерфейсов формы ответов API для всех микро-сервисов, модели запросов и единого обработчика исключений для приложения.

## Установка

Чтобы установить пакет, выполните следующие команды:

```bash
composer require radisand/api-general-scheme-mygenetics
--- --- --- --- --- --- --- --- --- ---
php artisan vendor:publish --tag=config
```

В файле конфигурации **config/myGeneticsApiScheme.php** можно изменить значения для ключей стандартного ответа API.