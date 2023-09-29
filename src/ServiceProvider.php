<?php

namespace Gedachtegoed\Workspace;

use Gedachtegoed\Workspace\Core\Aggregator;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [Aggregator::class];
    }

    public function boot(): void
    {
        if (! $this->app->environment(['local', 'testing'])) {
            return;
        }

        $this->app->singleton(
            Aggregator::class,
            fn () => new Aggregator
        );

        $this->publishes([
            __DIR__ . '/../config/workspace-integrations.php' => base_path('config/workspace-integrations.php'),
        ], 'workspace-config');

        $this->registerCommandAliasses();
        $this->registerIntegrationConfig();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/workspace-integrations.php', 'workspace-integrations');
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
            'workspace-3rd-party-configs'
        );

        $this->publishes(
            $integrations->publishesWorkflows(),
            'workspace-workflows'
        );
    }
}
