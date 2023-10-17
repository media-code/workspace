<?php

namespace Gedachtegoed\Workspace;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->environment(['local', 'testing'])) {
            return; // @codeCoverageIgnore
        }

        $this->publishes([
            __DIR__ . '/../config/workspace-integrations.php' => base_path('config/workspace-integrations.php'),
        ], 'workspace-config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/workspace-integrations.php', 'workspace-integrations');
    }
}
