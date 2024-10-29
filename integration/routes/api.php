<?php

use App\Http\Controllers\Api\LessonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'as' => 'lesson.',
    'prefix' => 'lesson',
    //'middleware' => ['auth', 'admin']
], function () {
    Route::get('member-lesson', [LessonController::class, 'memberLesson']);
});
