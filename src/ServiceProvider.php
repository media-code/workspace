<?php

namespace Gedachtegoed\Janitor;

use Gedachtegoed\Janitor\Core\Manager;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        if(! $this->app->environment(['local', 'testing'])) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/janitor-integrations.php' => base_path('config/janitor-integrations.php'),
        ], 'janitor-config');

        $this->app->singleton(
            Manager::class,
            fn() => new Manager()
        );

        $this->registerCommandAliasses();
        $this->registerIntegrationConfig();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/janitor-integrations.php', 'janitor-integrations');
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
        $manager = $this->app->make(Manager::class);

        $this->publishes(
            $manager->publishesConfigs(),
            'janitor-3rd-party-configs'
        );

        $this->publishes(
            $manager->publishesWorkflows(),
            'janitor-workflows'
        );
    }
}
