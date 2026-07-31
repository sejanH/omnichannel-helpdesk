<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OmnichannelController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('welcome'); })->name('home');
Route::get('/demo', function () { return view('demo'); })->name('demo');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [OmnichannelController::class, 'dashboard'])->name('dashboard');
    Route::get('/tickets/{ticket}/messages', [OmnichannelController::class, 'getMessages']);
    Route::post('/tickets/{ticket}/messages', [OmnichannelController::class, 'sendMessage']);
    Route::get('/tickets/{ticket?}', [OmnichannelController::class, 'tickets'])->name('tickets');
});


// Public & Admin Widget APIs
Route::prefix('api/v1/widget')->group(function () {
    Route::get('/config', [WidgetController::class, 'getConfig']);
    Route::post('/config/{channel}', [WidgetController::class, 'updateConfig']);
    Route::post('/init', [WidgetController::class, 'initSession']);
    Route::get('/messages', [WidgetController::class, 'getMessages']);
    Route::post('/messages', [WidgetController::class, 'sendMessage']);
});

// External Channel Webhooks (WhatsApp, Telegram, Facebook)
use App\Http\Controllers\WebhookController;

Route::prefix('api/v1/webhooks')->group(function () {
    Route::match(['get', 'post'], '/whatsapp', [WebhookController::class, 'handleWhatsApp']);
    Route::post('/telegram', [WebhookController::class, 'handleTelegram']);
    Route::match(['get', 'post'], '/facebook', [WebhookController::class, 'handleFacebook']);
});

// Error Pages Preview Route
Route::get('/errors/{code?}', function ($code = '404') {
    $validCodes = ['400', '401', '403', '404', '419', '429', '4xx', '500', '502', '503', '504', '5xx'];
    if (!in_array($code, $validCodes)) {
        $code = '404';
    }
    return response()->view("errors.{$code}", [], (int) (is_numeric($code) ? $code : 500));
})->name('errors.preview');


