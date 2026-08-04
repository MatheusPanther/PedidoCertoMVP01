<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));

        Gate::define('manage-settings', fn (User $user) => in_array($user->role, [
            UserRole::Owner->value,
            UserRole::Admin->value,
            UserRole::Manager->value,
        ], true));

        Gate::define('operate-orders', fn (User $user) => in_array($user->role, [
            UserRole::Owner->value,
            UserRole::Admin->value,
            UserRole::Manager->value,
            UserRole::Kitchen->value,
            UserRole::Cashier->value,
        ], true));

        Gate::define('handle-conversations', fn (User $user) => in_array($user->role, [
            UserRole::Owner->value,
            UserRole::Admin->value,
            UserRole::Manager->value,
            UserRole::Attendant->value,
        ], true));
    }
}
