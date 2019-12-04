<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class JsonProfile
{
    /**
     * Limit the profile data being returned. Values are based off the Laravel Debugbar:
     *
     * __meta, auth, php, messages, time, laravel, evetns, logs, files, config, models, memory, exceptions, views, route, queries, swiftmailer_mails, gate, session, request
     * @var array
     */
    protected array $profilingData = [
        '__meta',
        'auth',
        'php',
        'messages',
        'time',
        'laravel',
        'events',
        'logs',
        'files',
        'config',
        'cache',
        'models',
        'memory',
        'exceptions',
        'views',
        'route',
        'queries',
        'swiftmailer_mails',
        'gate',
        'session',
        'request'
    ];

    public function handle(Request $request, Closure $next)
    {
        $response =  $next($request);

        if (!app()->bound('debugbar') || !app('debugbar')->isEnabled()) {
            return $response;
        }

        if ($response instanceof JsonResponse && ! is_null($request->header('X-REQUEST-DEBUG'))) {
            $response->setData(array_merge($response->getData(true), [
                '_profile' => $this->getProfilingData()
            ]));
        }

        return $response;
    }

    protected function getProfilingData()
    {
        return Arr::only(app('debugbar')->getData(), $this->profilingData);
    }
}
