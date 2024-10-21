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

    'ai' => [
        'api_key' => env('AI_API_KEY'),
        'base_url' => env('AI_BASE_URL'),
        'chat' => [
            'echo' => '/chat/echo',
            'converse' => [
                'base' => '/chat/converse',
                'starter-message' => '/chat/converse/starter-message',
                'text' => '/chat/converse/text',
            ]
        ],
        'bot' => [
            'types' => '/bot/types',
        ],
        'client' => [
            'quota' => '/client/quota',
        ]
    ]

];
