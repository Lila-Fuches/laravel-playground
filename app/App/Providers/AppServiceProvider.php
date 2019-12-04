<?php

namespace App\Providers;

use Support\JwtGuard;
use App\BaseApplication;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Spatie\QueryString\QueryString;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(QueryString::class, function () {
            $request = $this->app->get(Request::class);

            return new QueryString(urldecode($request->getRequestUri()));
        });

        $auth = $this->app->make(AuthManager::class);
        $auth->extend('jwt', function (BaseApplication $app, string $name, array $config) use ($auth) {
            $guard = new JwtGuard($auth->createUserProvider($config['provider'] ?? null), $name, $config);
            $guard
                // Set the request instance on the guard
                ->setRequest($app->refresh('request', $guard, 'setRequest'))
                // Set the event dispatcher on the guard
                ->setDispatcher($this->app['events']);
            return $guard;
        });
    }
}
