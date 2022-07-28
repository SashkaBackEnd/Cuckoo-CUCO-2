<?php

namespace App\Providers;

use App\GuardedObject;
use App\Observers\GuardedObjectObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        GuardedObject::observe(GuardedObjectObserver::class);
    }
}
