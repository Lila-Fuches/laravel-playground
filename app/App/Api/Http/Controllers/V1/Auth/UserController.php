<?php

namespace App\Api\Http\Controllers\V1\Auth;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    public function __invoke(Request $request)
    {
        return response()->json(
            $request->user(),
            Response::HTTP_OK
        );
    }
}
