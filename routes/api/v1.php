<?php

use Symfony\Component\HttpFoundation\Response;

Route::get('ping', function () {
    return response()->json([
        'ack' => time()
    ], Response::HTTP_OK);
});
