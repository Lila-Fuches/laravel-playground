<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VndMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $name = Str::slug(config('app.name'), '.');
        $response->header(
            'Content-Type',
            "application/vnd.{$name}+json"
        );

        return $response;
    }
}
