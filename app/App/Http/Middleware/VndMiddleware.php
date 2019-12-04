<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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
