<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Game Provider Services
    'apigames' => [
        'base_url' => env('APIGAMES_BASE_URL', 'https://v1.apigames.id'),
        'merchant_id' => env('APIGAMES_MERCHANT_ID'),
        'signature' => env('APIGAMES_SIGNATURE'),
    ],

    'codashop' => [
        'base_url' => env('CODASHOP_BASE_URL', 'https://order.codashop.com'),
        'api_key' => env('CODASHOP_API_KEY'),
    ],

    'duniagames' => [
        'base_url' => env('DUNIAGAMES_BASE_URL', 'https://api.duniagames.co.id'),
        'api_key' => env('DUNIAGAMES_API_KEY'),
    ],

];
