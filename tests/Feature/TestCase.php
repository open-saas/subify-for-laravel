<?php

namespace Tests\Feature;

use OpenSaaS\Subify\SubifyServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            SubifyServiceProvider::class,
        ];
    }
}
