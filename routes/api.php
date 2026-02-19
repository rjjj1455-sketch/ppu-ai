<?php
use App\Http\Controllers\LayananPublikController;
use Illuminate\Support\Facades\Route;
Route::post('/layanan-publik', [LayananPublikController::class, 'store']);
Route::post('/layanan-publik/chat', [LayananPublikController::class, 'simpanChat']);