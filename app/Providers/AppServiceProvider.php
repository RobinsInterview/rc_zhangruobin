<?php

namespace App\Providers;

use App\Services\Auth\AuthenticatorInterface;
use App\Services\Auth\NullAuthenticator;
use App\Services\Notifications\CodeEventDefinitionProvider;
use App\Services\Notifications\Contracts\EventDefinitionProviderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticatorInterface::class, NullAuthenticator::class);
        $this->app->singleton(EventDefinitionProviderInterface::class, CodeEventDefinitionProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
