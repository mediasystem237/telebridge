<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Bot Configuration
    |--------------------------------------------------------------------------
    */
    'bot' => [
        'token' => env('TELEGRAM_BOT_TOKEN', 'your-default-bot-token-here'),
        'username' => env('TELEGRAM_BOT_USERNAME', 'YourBotUsername'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Multiple Bots Configuration
    |--------------------------------------------------------------------------
    | Vous pouvez configurer plusieurs bots ici.
    | Utilisez TeleBridge::bot('name') pour accéder à un bot spécifique.
    */
    'bots' => [
        'default' => [
            'token' => env('TELEGRAM_BOT_TOKEN'),
            'username' => env('TELEGRAM_BOT_USERNAME'),
        ],
        
        // Exemple de bot supplémentaire
        // 'support' => [
        //     'token' => env('TELEGRAM_SUPPORT_BOT_TOKEN'),
        //     'username' => env('TELEGRAM_SUPPORT_BOT_USERNAME'),
        // ],
        
        // 'notifications' => [
        //     'token' => env('TELEGRAM_NOTIF_BOT_TOKEN'),
        //     'username' => env('TELEGRAM_NOTIF_BOT_USERNAME'),
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    */
    'webhook' => [
        'path' => 'telebridge/webhook',
        'secret_token' => env('TELEGRAM_WEBHOOK_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'default_parse_mode' => 'Markdown', // 'Markdown', 'HTML', or null
    ],

];
