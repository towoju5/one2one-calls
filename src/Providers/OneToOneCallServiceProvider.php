<?php

namespace Towoju\One2OneCalls\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class OneToOneCallServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/one2one-calls.php', 'one2one-calls');
    }

    public function boot(): void
    {
        // Policies / Gates
        Gate::define('manage-call-permissions', function ($user) {
            // If the app uses Spatie\Permission, this will respect 'Super Admin' role
            if (method_exists($user, 'hasRole')) {
                return $user->hasRole(config('one2one-calls.super_admin_role', 'Super Admin'));
            }
            // fallback boolean on the users table
            return (bool) data_get($user, 'is_super_admin');
        });

        // Routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/channels.php');

        // Views & assets
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'one2one-calls');

        // Migrations
        if (! class_exists('CreateCallsTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/' => database_path('migrations'),
            ], 'one2one-calls-migrations');
        }

        // Config
        $this->publishes([
            __DIR__ . '/../../config/one2one-calls.php' => config_path('one2one-calls.php'),
        ], 'one2one-calls-config');

        // Views & JS stubs
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/one2one-calls'),
            __DIR__ . '/../../resources/js' => resource_path('js/vendor/one2one-calls'),
        ], 'one2one-calls-assets');
    }
}
