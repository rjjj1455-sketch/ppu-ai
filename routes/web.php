<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/', [ChatController::class, 'index']);
Route::post('/chat/send', [ChatController::class, 'sendMessage']);
Route::delete('/chat/{id}', [ChatController::class, 'deleteChat']);
Route::delete('/chat/all/clear', [ChatController::class, 'clearAllChats']);
