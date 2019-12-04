<?php

namespace App\Providers;

use Domain\User\Models\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Api\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->mapBindings();

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapV1Routes();
    }

    protected function mapBindings(): void
    {
        Route::bind('user', function (string $uuid): User {
            return User::whereUuid($uuid)->firstOrFail();
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapV1Routes()
    {
        Route::prefix('v1')
             ->middleware('api')
             ->namespace("{$this->namespace}\V1")
             ->group(base_path('routes/api/v1.php'));
    }
}
