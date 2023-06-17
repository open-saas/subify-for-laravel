<?php

namespace OpenSaaS\Subify;

use Illuminate\Support\ServiceProvider;

class SubifyServiceProvider extends ServiceProvider
{
    public array $bindings = [
        Contracts\Cache\BenefitPlanRepository::class => Repositories\Cache\BenefitPlanRepository::class,
        Contracts\Cache\BenefitRepository::class => Repositories\Cache\BenefitRepository::class,
        Contracts\Cache\BenefitUsageRepository::class => Repositories\Cache\BenefitUsageRepository::class,
        Contracts\Cache\SubscriptionRepository::class => Repositories\Cache\SubscriptionRepository::class,
        Contracts\Context\BenefitPlanRepository::class => Repositories\Context\BenefitPlanRepository::class,
        Contracts\Context\BenefitRepository::class => Repositories\Context\BenefitRepository::class,
        Contracts\Context\BenefitUsageRepository::class => Repositories\Context\BenefitUsageRepository::class,
        Contracts\Context\SubscriptionRepository::class => Repositories\Context\SubscriptionRepository::class,
        Contracts\Database\BenefitPlanRepository::class => Repositories\Eloquent\BenefitPlanRepository::class,
        Contracts\Database\BenefitRepository::class => Repositories\Eloquent\BenefitRepository::class,
        Contracts\Database\BenefitUsageRepository::class => Repositories\Eloquent\BenefitUsageRepository::class,
        Contracts\Database\SubscriptionRepository::class => Repositories\Eloquent\SubscriptionRepository::class,
        Contracts\Decorators\BenefitDecorator::class => Decorators\BenefitDecorator::class,
        Contracts\Decorators\BenefitPlanDecorator::class => Decorators\BenefitPlanDecorator::class,
        Contracts\Decorators\BenefitUsageDecorator::class => Decorators\BenefitUsageDecorator::class,
        Contracts\Decorators\SubscriptionDecorator::class => Decorators\SubscriptionDecorator::class,
    ];

    public array $singletons = [
        Contracts\SubscriptionManager::class => SubscriptionManager::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/subify.php', 'subify');

        $this->listenForEvents();
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

    protected function listenForEvents()
    {
        $this->app['events']->listen(
            [
                \Laravel\Octane\Events\RequestReceived::class,
                \Laravel\Octane\Events\TaskReceived::class,
                \Laravel\Octane\Events\TickReceived::class,
            ],
            fn () => $this->app[Contracts\SubscriptionManager::class]->flushContext(),
        );

        $this->app['events']->listen(
            [
                \Illuminate\Queue\Events\JobProcessed::class,
            ],
            fn () => $this->app[Contracts\SubscriptionManager::class]->flushContext(),
        );
    }
}
