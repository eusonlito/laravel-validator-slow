<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'name' => 'Validator benchmarks',
    'env' => 'production',
    'debug' => false,
    'locale' => 'en',
    'fallback_locale' => 'en',
    'key' => 'test',
    'cipher' => 'AES-256-CBC',
    'maintenance' => [
        'driver' => 'file',
    ],
    'providers' => ServiceProvider::defaultProviders()->toArray(),
    'aliases' => Facade::defaultAliases()->toArray(),

];
