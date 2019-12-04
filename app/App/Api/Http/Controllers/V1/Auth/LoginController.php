<?php

namespace App\Api\Http\Controllers\V1\Auth;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Api\Http\Requests\V1\Auth\LoginRequest;

class LoginController
{
    public function __invoke(LoginRequest $request)
    {
        $token = Auth::attempt($request->only('email', 'password'));

        return response()->json([
            'token' => $token->__toString()
        ], Response::HTTP_OK);
    }
}
