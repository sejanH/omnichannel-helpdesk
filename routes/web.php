<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OmnichannelController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

Route::get('/widget.js', function () {
    $path = public_path('widget.js');
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, [
        'Content-Type' => 'application/javascript; charset=UTF-8',
        'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
        'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
        'Access-Control-Allow-Origin' => '*',
    ]);
});

use App\Http\Controllers\DocsController;
Route::get('/docs/{page?}', [DocsController::class, 'show'])->where('page', '.*')->name('docs.show');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\AgentController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WidgetBuilderController;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [OmnichannelController::class, 'dashboard'])->name('dashboard');
    Route::get('/tickets/{ticket}/messages', [OmnichannelController::class, 'getMessages']);
    Route::post('/tickets/{ticket}/messages', [OmnichannelController::class, 'sendMessage']);
    Route::get('/tickets/{ticket?}', [OmnichannelController::class, 'tickets'])->name('tickets');
    Route::patch('/tickets/{ticket}/resolve', [OmnichannelController::class, 'resolveTicket'])->name('tickets.resolve');
    Route::patch('/tickets/{ticket}/assign', [OmnichannelController::class, 'assignAgent'])->name('tickets.assign');

    // Subscription & Billing Routes
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/plan', [BillingController::class, 'updatePlan'])->name('billing.plan.update');
    Route::post('/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
    Route::post('/billing/resume', [BillingController::class, 'resume'])->name('billing.resume');
    Route::post('/billing/verify-license', [BillingController::class, 'verifyLicense'])->name('billing.verify_license');

    // Reports & Performance Analytics (Admin Only)
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

    // Agent & CRM User Management Routes
    Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
    Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
    Route::post('/agents/{agent}/toggle', [AgentController::class, 'toggleStatus'])->name('agents.toggle');
    Route::delete('/agents/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');

    // Logged in User Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/status', [ProfileController::class, 'updateStatus'])->name('profile.status');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Multi-Widget Builder Studio Routes
    Route::get('/widget-builder', [WidgetBuilderController::class, 'index'])->name('widget-builder.index');
    Route::post('/widget-builder', [WidgetBuilderController::class, 'store'])->name('widget-builder.store');
    Route::put('/widget-builder/{channel}', [WidgetBuilderController::class, 'update'])->name('widget-builder.update');
    Route::delete('/widget-builder/{channel}', [WidgetBuilderController::class, 'destroy'])->name('widget-builder.destroy');
});

// Stripe Webhook Endpoint (Exempt from CSRF)
Route::post('/stripe/webhook', [BillingController::class, 'stripeWebhook'])->name('stripe.webhook');


// Public & Admin Widget APIs
Route::prefix('api/v1/widget')->group(function () {
    Route::get('/config', [WidgetController::class, 'getConfig']);
    Route::post('/config/{channel}', [WidgetController::class, 'updateConfig']);
    Route::post('/init', [WidgetController::class, 'initSession']);
    Route::get('/messages', [WidgetController::class, 'getMessages']);
    Route::post('/messages', [WidgetController::class, 'sendMessage']);
    Route::post('/rating', [WidgetController::class, 'submitRating']);
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


