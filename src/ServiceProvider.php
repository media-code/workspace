<?php

namespace Gedachtegoed\Janitor;

use Gedachtegoed\Janitor\Core\Aggregator;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->environment(['local', 'testing'])) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/janitor-integrations.php' => base_path('config/janitor-integrations.php'),
        ], 'janitor-config');

        $this->app->singleton(
            Aggregator::class,
            fn () => new Aggregator
        );

        $this->registerCommandAliasses();
        $this->registerIntegrationConfig();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/janitor-integrations.php', 'janitor-integrations');
    }

    protected function registerCommandAliasses()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\Integrate::class,
                Commands\Install::class,
                Commands\Update::class,
            ]);
        }
    }

    protected function registerIntegrationConfig()
    {
        $integrations = $this->app->make(Aggregator::class);

        $this->publishes(
            $integrations->publishesConfigs(),
            'janitor-3rd-party-configs'
        );

        $this->publishes(
            $integrations->publishesWorkflows(),
            'janitor-workflows'
        );
    }
}
