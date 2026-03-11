<?php

namespace App\Providers;

use App\Models\ShortUrl;
use App\Policies\ShortUrlPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicitly register ShortUrlPolicy to enforce ownership authorization.
        Gate::policy(ShortUrl::class, ShortUrlPolicy::class);
    }
}
