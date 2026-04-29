<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ProjectUpdateController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {

    Route::post('/register', [
        AuthController::class,
        'register'
    ]);

    Route::post('/login', [
        AuthController::class,
        'login'
    ]);

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/me', [
            AuthController::class,
            'me'
        ]);

        Route::post('/logout', [
            AuthController::class,
            'logout'
        ]);
    });
});


// PUBLIC ROUTES

Route::get('/reports', [
    ReportController::class,
    'index'
]);

Route::get('/reports/{id}', [
    ReportController::class,
    'show'
]);


// PROTECTED ROUTES

Route::middleware('auth:sanctum')->group(function () {

    // REPORTS

    Route::post('/reports', [
        ReportController::class,
        'store'
    ]);

    Route::delete('/reports/{id}', [
        ReportController::class,
        'destroy'
    ]);

    

    // COMMENTS

    Route::post('/comments', [
        CommentController::class,
        'store'
    ]);

    Route::delete('/comments/{id}', [
        CommentController::class,
        'destroy'
    ]);

    // PROJECT UPDATES

    Route::post('/project-updates', [
        ProjectUpdateController::class,
        'store'
    ]);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::patch('/reports/{id}/status', [
        ReportController::class,
        'updateStatus'
    ]);
});