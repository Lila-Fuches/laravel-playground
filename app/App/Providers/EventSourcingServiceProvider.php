<?php

namespace App\Providers;

use Spatie\EventSourcing\Facades\Projectionist;

class EventSourcingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
//        // adding a single projector
//        Projectionist::addProjector(AccountBalanceProjector::class);
//
//        // you can also add multiple projectors in one go
//        Projectionist::addProjectors([
//            AnotherProjector::class,
//            YetAnotherProjector::class,
//        ]);
    }
}
