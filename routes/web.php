<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LayananPublikController;
use App\Http\Controllers\Admin\ValidasiController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat', [ChatController::class, 'sendMessage'])->name('chat.send');
Route::delete('/chat/{id}', [ChatController::class, 'deleteChat']);
Route::delete('/chat/all/clear', [ChatController::class, 'clearAllChats']);

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/chart-data', [DashboardController::class, 'chartData'])->name('chart.data');

        Route::get('/layanan/export-csv', [LayananPublikController::class, 'exportCsv'])->name('layanan.export');
        Route::resource('layanan', LayananPublikController::class);

        Route::get('/validasi', [ValidasiController::class, 'index'])->name('validasi.index');
        Route::patch('/validasi/{layanan}/status', [ValidasiController::class, 'updateStatus'])->name('validasi.update');
        Route::post('/validasi/bulk-update', [ValidasiController::class, 'bulkUpdate'])->name('validasi.bulk');
    });