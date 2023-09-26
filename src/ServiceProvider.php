<?php

namespace Gedachtegoed\Janitor;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/janitor-integrations.php' => base_path('config/janitor-integrations.php'),
        ], 'janitor-config');

        $this->registerCommandAliasses();
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
}
