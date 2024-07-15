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
], function () {
    Route::get('webhooks/list', [MKWebhookLogController::class, 'list'])->name('list');
    Route::get('webhook/{log}', [MKWebhookLogController::class, 'info'])->name('info');
});

Route::group([
    'as' => 'getcource.',
    'prefix' => 'getcource',
], function () {
    Route::get('updates/list', [GKLogController::class, 'list'])->name('list');
    Route::get('update/{log}', [GKLogController::class, 'info'])->name('info');
});

Route::group([
    'as' => 'user-notification.',
    'prefix' => 'user-notification',
    'middleware' => ['auth']
], function () {
    Route::get('users', [UserNotificationController::class, 'list'])->name('users');
    Route::get('user/{user}', [UserNotificationController::class, 'info'])->name('info');
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
