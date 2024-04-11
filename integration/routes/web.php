<?php

use App\Http\Controllers\GKLogController;
use App\Http\Controllers\MKWebhookLogController;
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
