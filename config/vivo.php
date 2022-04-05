<?php

return [


    'shopify' => [

        'key'       => env('SHOP_API_KEY_UK'),
        'password'  => env('SHOP_PASSWORD_UK'),
        'secret'    => env('SHOP_SHARED_SECRET_UK'),
        'url'       => env('SHOP_URL_UK'),
        'webhook_secret' => env('SHOP_WEBHOOK_SECRET_UK'),

        'uk'       => [
            'key'       => env('SHOP_API_KEY_UK'),
            'password'  => env('SHOP_PASSWORD_UK'),
            'secret'    => env('SHOP_SHARED_SECRET_UK'),
            'url'       => env('SHOP_URL_UK'),
            'front'       => env('SHOP_FRONT_UK'),
            'currency'       => env('SHOP_CURRENCY_UK'),
            'webhook_secret' => env('SHOP_WEBHOOK_SECRET_UK')
    
        ],
        'eu'    => [
            'key'       => env('SHOP_API_KEY_EU'),
            'password'  => env('SHOP_PASSWORD_EU'),
            'secret'    => env('SHOP_SHARED_SECRET_EU'),
            'url'       => env('SHOP_URL_EU'),
            'front'       => env('SHOP_FRONT_EU'),
            'currency'       => env('SHOP_CURRENCY_EU'),
            'webhook_secret' => env('SHOP_WEBHOOK_SECRET_EU')
        ],
        'us'    => [
            'key'       => env('SHOP_API_KEY_US'),
            'password'  => env('SHOP_PASSWORD_US'),
            'secret'    => env('SHOP_SHARED_SECRET_US'),
            'url'       => env('SHOP_URL_US'),
            'front'       => env('SHOP_FRONT_US'),
            'currency'       => env('SHOP_CURRENCY_US'),
            'webhook_secret' => env('SHOP_WEBHOOK_SECRET_US')
        ],
        'de'    => [
            'key'       => env('SHOP_API_KEY_DE'),
            'password'  => env('SHOP_PASSWORD_DE'),
            'secret'    => env('SHOP_SHARED_SECRET_DE'),
            'url'       => env('SHOP_URL_UK'),
            'front'       => env('SHOP_FRONT_DE'),
            'currency'       => env('SHOP_CURRENCY_DE'),
            'webhook_secret' => env('SHOP_WEBHOOK_SECRET_DE')
        ],
        'fr'    => [
            'key'       => env('SHOP_API_KEY_FR'),
            'password'  => env('SHOP_PASSWORD_FR'),
            'secret'    => env('SHOP_SHARED_SECRET_FR'),
            'url'       => env('SHOP_URL_FR'),
            'front'       => env('SHOP_FRONT_FR'),
            'currency'       => env('SHOP_CURRENCY_FR'),
            'webhook_secret' => env('SHOP_WEBHOOK_SECRET_FR')
        ],
        'it'    => [
            'key'       => env('SHOP_API_KEY_IT'),
            'password'  => env('SHOP_PASSWORD_IT'),
            'secret'    => env('SHOP_SHARED_SECRET_IT'),
            'url'       => env('SHOP_URL_IT'),
            'front'       => env('SHOP_FRONT_IT'),
            'currency'       => env('SHOP_CURRENCY_IT'),
            'webhook_secret' => env('SHOP_WEBHOOK_SECRET_IT')
        ]

    ],

    'ometria' => [

        'api_key'           => env('OMETRIA_API'),
        'bis_segment_id'    => env('OMETRIA_BIS_SEGMENT_ID'),
        'bis_collection'    => env('OMETRIA_BIS_COLLECTION_NAME'),

    ],
];