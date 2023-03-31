<?php

namespace OpenSaaS\Subify;

use Illuminate\Support\ServiceProvider;

class SubifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/subify.php', 'subify');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->offerPublishing();
        }
    }

    protected function offerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/subify.php' => config_path('subify.php'),
        ], 'subify-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
        ], 'subify-migrations');
    }
}
