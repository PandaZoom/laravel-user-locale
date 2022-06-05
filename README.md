## Laravel Locale

Support a locale on Laravel for stateless app

### Minimum requirements

Php `^8.0`

## Using

1. publish migration: `php artisan vendor:publsih --provider="PandaZoom\LaravelLocale\Providers\AppServiceProvider"`
2. run migration `php artisan migrate`. Now user model has a `locale` field, please correct requests `GET|PUT|PATCH` on
   user model for using a new property.
3. Add list of the supported locales to config file `app.locales` as `array`, example:

```php
<?php

return [
    //...
    'locales' => ['en', 'de', 'fr'],
    //...
];
```

4. Use middleware `PandaZoom\LaravelLocale\Http\Middleware\AcceptLanguageFallback` on required `routes`, `routes groups`
   , `controllers` ...etc. or set globally at `App\Http\Kernel.php` 
