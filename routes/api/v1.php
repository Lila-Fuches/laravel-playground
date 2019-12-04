<?php

use Symfony\Component\HttpFoundation\Response;

Route::get('ping', function () {
    return response()->json([
        'ack' => time()
    ], Response::HTTP_OK);
})->name('ping');

Route::prefix('auth')->namespace('Auth')->as('auth.')->group(function () {

    /**
     * Login Request
     */
    Route::post('login', LoginController::class)->name('login');
});

Route::middleware(['auth:api'])->group(function () {

    Route::prefix('user')->namespace('Auth')->as('auth.')->group(function () {
        Route::get('/', UserController::class)->name('user');
    });
});

