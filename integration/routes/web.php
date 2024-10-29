<?php

use App\Http\Controllers\GKLogController;
use App\Http\Controllers\MKWebhookLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserNotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group([
    'as' => 'moyklass.',
    'prefix' => 'moyklass',
    'middleware' => ['auth.key', 'auth', 'admin']
], function () {
    Route::get('webhooks/list', [MKWebhookLogController::class, 'list'])->name('list');
    Route::get('webhook/{log}', [MKWebhookLogController::class, 'info'])->name('info');
});

Route::group([
    'as' => 'getcource.',
    'prefix' => 'getcource',
    'middleware' => ['auth.key', 'auth', 'admin']
], function () {
    Route::get('updates/list', [GKLogController::class, 'list'])->name('list');
    Route::get('update/{log}', [GKLogController::class, 'info'])->name('info');
});

Route::group([
    'as' => 'user-notification.',
    'prefix' => 'user-notification',
    'middleware' => ['auth.key', 'auth', 'admin']
], function () {
    Route::get('users', [UserNotificationController::class, 'list'])->name('users');
    Route::get('user/{hash}', [UserNotificationController::class, 'info'])->name('info');
    Route::post('user/{hash}', [UserNotificationController::class, 'save'])->name('save');
    Route::post('user/check-email/{member}', [UserNotificationController::class, 'sendCodeForEmail'])->name('sendCodeForEmail');
    Route::get('verify-email', [UserNotificationController::class, 'verifyEmail'])->name('verifyCodeForEmail');

    Route::any('/get-updates', [UserNotificationController::class, 'telegramStart'])->name('start');
    Route::post('/telegram-subscribe/{member}', [UserNotificationController::class, 'telegramSubscribe'])->name('telegramSubscribe');
    Route::post('/send-whatsapp-code/{member}', [UserNotificationController::class, 'sendWhatsappCode'])->name('sendCodeForWhatsapp');
    Route::post('/check-whatsapp-code/{member}', [UserNotificationController::class, 'checkWhatsappCode'])->name('checkCodeForWhatsapp');

});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
