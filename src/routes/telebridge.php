<?php

use Illuminate\Support\Facades\Route;
use Mbindi\Telebridge\Http\Controllers\TeleBridgeController;

Route::post('/telebridge/webhook/{bot_token}', [TeleBridgeController::class, 'handleWebhook'])
    ->name('telebridge.webhook');
