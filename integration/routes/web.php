<?php

use App\Http\Controllers\MKWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group([
    'as' => 'moyklass.',
    'prefix' => 'moyklass',
], function () {
    Route::get('webhooks', [MKWebhookController::class, 'list'])->name('list');
    Route::get('hook_info/{log}', [MKWebhookController::class, 'hookInfo'])->name('info');
});
