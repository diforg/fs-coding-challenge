<?php

use App\Http\Controllers\Webhook\WhatsappWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhook')->group(function () {
    Route::post('whatsapp/received', [WhatsappWebhookController::class, 'handleMessageReceived'])
        ->name('webhook.whatsapp.received');
});