<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS Paths
    |--------------------------------------------------------------------------
    |
    | You can enable CORS for 1 or multiple paths.
    | Example: ['api/*', 'sanctum/csrf-cookie']
    */

    'paths' => ['api/*'],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Methods
    |--------------------------------------------------------------------------
    |
    | The allowed methods can be specified as an array of strings.
    | Example: ['GET', 'POST', 'PUT', 'DELETE']
    | '*': Allow all methods.
    */

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Origins
    |--------------------------------------------------------------------------
    |
    | The allowed origins can be specified as an array of strings.
    | Example: ['http://localhost:3000']
    | '*': Allow all origins.
    */

    'allowed_origins' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | The allowed origins can be specified as an array of patterns.
    | Example: ['http://localhost:*']
    */

    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Headers
    |--------------------------------------------------------------------------
    |
    | The allowed headers can be specified as an array of strings.
    | Example: ['Content-Type', 'X-Auth-Token']
    | '*': Allow all headers.
    */

    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | CORS Exposed Headers
    |--------------------------------------------------------------------------
    |
    | The exposed headers can be specified as an array of strings.
    | Example: ['Authorization', 'X-Custom-Header']
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | CORS Max Age
    |--------------------------------------------------------------------------
    |
    | The max age can be specified as an integer.
    */

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | CORS Supports Credentials
    |--------------------------------------------------------------------------
    |
    | The supports credentials can be specified as a boolean.
    */

    'supports_credentials' => false,

];
