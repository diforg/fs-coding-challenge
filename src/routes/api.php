<?php

use App\Http\Controllers\Webhook\WhatsappWebhookController;
use App\Http\Controllers\Webhook\MessengerWebhookController;
use App\Http\Controllers\Webhook\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhook')->group(function () {
    Route::post('whatsapp/received', [WhatsappWebhookController::class, 'handleMessageReceived'])
        ->name('webhook.whatsapp.received');
    Route::post('messenger/received', [MessengerWebhookController::class, 'handleMessageReceived'])
        ->name('webhook.messenger.received');
    Route::post('telegram/received', [TelegramWebhookController::class, 'handleMessageReceived'])
        ->name('webhook.telegram.received');
});