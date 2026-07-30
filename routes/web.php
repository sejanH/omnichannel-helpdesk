<?php

use App\Http\Controllers\OmnichannelController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OmnichannelController::class, 'dashboard'])->name('dashboard');
Route::get('/demo', function () { return view('demo'); })->name('demo');
Route::get('/tickets/{ticket}/messages', [OmnichannelController::class, 'getMessages']);
Route::post('/tickets/{ticket}/messages', [OmnichannelController::class, 'sendMessage']);


// Public & Admin Widget APIs
Route::prefix('api/v1/widget')->group(function () {
    Route::get('/config', [WidgetController::class, 'getConfig']);
    Route::post('/config/{channel}', [WidgetController::class, 'updateConfig']);
    Route::post('/init', [WidgetController::class, 'initSession']);
    Route::get('/messages', [WidgetController::class, 'getMessages']);
    Route::post('/messages', [WidgetController::class, 'sendMessage']);
});

