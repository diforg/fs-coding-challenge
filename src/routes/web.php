<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', [ChatController::class, 'index']);
Route::post('/read-message', [ChatController::class, 'readMessage']);
Route::post('/send-message', [ChatController::class, 'sendMessage']);
Route::get('/list-contacts', [ChatController::class, 'modalContacts']);