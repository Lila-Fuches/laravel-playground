<?php

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

Route::get('ping', function () {
    return response()->json([
        'ack' => time()
    ], Response::HTTP_OK);
})->name('ping');

Route::prefix('auth')->namespace('Auth')->as('auth.')->group(function () {

    /**
     * Login
     */
    Route::post('login', LoginController::class)->name('login');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
