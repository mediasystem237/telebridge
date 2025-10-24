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
    | Webhook Settings
    |--------------------------------------------------------------------------
    */
    'webhook' => [
        'path' => 'telebridge/webhook',
        'secret_token' => env('TELEGRAM_WEBHOOK_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Intent Detection Engine
    |--------------------------------------------------------------------------
    */
    'intent' => [
        'driver' => 'regex', // 'regex' or 'ai'
        'confidence_threshold' => 0.75,
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Driver Settings
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'provider' => 'openai', // e.g., 'openai', 'deepseek'
        'api_key' => env('AI_API_KEY'),
        'model' => env('AI_MODEL', 'gpt-3.5-turbo'),
    ],

];
