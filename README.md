[![Project Management](https://img.shields.io/badge/project-management-blue.svg)](https://waffle.io/neomerx/cors-illuminate)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/neomerx/cors-illuminate/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/neomerx/cors-illuminate/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/neomerx/cors-illuminate/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/neomerx/cors-illuminate/?branch=master)
[![Build Status](https://travis-ci.org/neomerx/cors-illuminate.svg?branch=master)](https://travis-ci.org/neomerx/cors-illuminate)
[![HHVM](https://img.shields.io/hhvm/neomerx/cors-illuminate.svg)](https://travis-ci.org/neomerx/cors-illuminate)
[![License](https://img.shields.io/packagist/l/neomerx/cors-illuminate.svg)](https://packagist.org/packages/neomerx/cors-illuminate)

## Description

This package adds [Cross-Origin Resource Sharing](http://www.w3.org/TR/cors/) (CORS) support to your Laravel application.

The package is based on [Framework agnostic (PSR-7) CORS implementation](https://github.com/neomerx/cors-psr7).

## Install

### 1 Composer

```
composer require neomerx/cors-illuminate
```

### 2.1 Laravel

> For Lumen skip this step and see step 2.2

Add CORS provider by adding the following line to your `config/app.php` file
```php
<?php

return [

    ...

    'providers' => [

        ...

        \Neomerx\CorsIlluminate\Providers\LaravelServiceProvider::class,

    ],
    
    ...

];
```

Add CORS middleware to your HTTP stack at `app/Http/Kernel.php` file. The middleware should be added to `$middleware` list which is executed for all routes (even non declared in your routes file). Preferably before 'heavy' middleware for performance reasons.

```php
class Kernel extends HttpKernel
{
    ...

    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Neomerx\CorsIlluminate\CorsMiddleware::class, // <== add this line
        
        ...
    ];
    
    ...
}
```

Create a config file by executing

```
php artisan vendor:publish --provider="Neomerx\CorsIlluminate\Providers\LaravelServiceProvider"
```

it will create `config/cors-illuminate.php` file in you application.

### 2.2 Lumen

> For Laravel skip this step

In `bootstrap/app.php` add CORS to global middleware list

```php
$app->middleware([
    ...
    \Neomerx\CorsIlluminate\CorsMiddleware::class,
]);
```

and register CORS provider

```php
$app->register(\Neomerx\CorsIlluminate\Providers\LumenServiceProvider::class);
```

As Lumen does not support `vendor:publish` command file `vendor/neomerx/cors-illuminate/config/cors-illuminate.php` have to be manually copied to `config/cors-illuminate.php`.

### 3 Configuration

[Configuration file](config/cors-illuminate.php) is extensively commented so it will be easy for you to set up it for your needs. First settings you need to configure are server origin (URL) and allowed origins

```php
    ...
    
    /**
     * Could be string or array. If specified as array (recommended for
     * better performance) it should be in parse_url() result format.
     */
    Settings::KEY_SERVER_ORIGIN => [
        'scheme' => 'http',
        'host'   => 'localhost',
        'port'   => 1337,
    ],

    /**
     * A list of allowed request origins (lower-cased, no trail slashes).
     * Value `true` enables and value `null` disables origin.
     * If value is not on the list it is considered as not allowed.
     * Environment variables could be used for enabling/disabling certain hosts.
     */
    Settings::KEY_ALLOWED_ORIGINS => [
        'http://localhost:4200' => true,
    ],
    
    ...
```

## Exceptions and CORS headers

When exceptions are thrown and responses are created in [Laravel/Lumen exception handlers](http://laravel.com/docs/5.1/errors) middleware will be excluded from handling responses. It means CORS middleware will not add its CORS headers to responses. For this reason CORS results (including headers) are registered in [Laravel/Lumen Container](http://laravel.com/docs/5.1/container) and made accessible from any part of your application including exception handlers.

Code sample for reading CORS headers

```php
$corsHeaders = [];
if (app()->resolved(AnalysisResultInterface::class) === true) {
    /** @var AnalysisResultInterface $result */
    $result = app(AnalysisResultInterface::class);
    $corsHeaders = $result->getResponseHeaders();
}
```

## Customization

This package provides a number of ways how its behaviour could be customized.

The following methods of class `CorsMiddleware` could be overriden
- `getResponseOnError` You can override this method in order to customize error reply.
- `getCorsAnalysis` You can override this method to modify how CORS analysis result is saved to Illuminate Container.
- `getRequestAdapter` You can override this method to replace `IlluminateRequestToPsr7` adapter with another one.

Additionally a custom [AnalysisStrategyInterface](https://github.com/neomerx/cors-psr7/blob/master/src/Contracts/AnalysisStrategyInterface.php) could be injected by
- overriding `getCreateAnalysisStrategyClosure` method in `ServiceProvider` for Laravel/Lumen
- using [Laravel/Lumen Container binding](http://laravel.com/docs/5.1/container) for interface `AnalysisStrategyInterface`

Also custom [AnalyzerInterface](https://github.com/neomerx/cors-psr7/blob/master/src/Contracts/AnalyzerInterface.php) could be injected by
- overriding `getCreateAnalyzerClosure` method in `ServiceProvider` for Laravel/Lumen
- using [Laravel/Lumen Container binding](http://laravel.com/docs/5.1/container) for interface `AnalyzerInterface`

## Testing

```
composer test
```

## Contributing

Pull requests for documentation and code improvements (PSR-2, tests) are welcome.

## Versioning

This package is using [Semantic Versioning](http://semver.org/).

## License

Apache License (Version 2.0). Please see [License File](LICENSE) for more information.
