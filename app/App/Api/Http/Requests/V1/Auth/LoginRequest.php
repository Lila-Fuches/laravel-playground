<?php

namespace App\Api\Http\Requests\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                'exists:users,email'
            ],
            'password' => [
                'required',
                'string'
            ]
        ];
    }
}
