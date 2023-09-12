<?php

namespace Gedachtegoed\Janitor;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../resources/config/duster.json' => base_path('duster.json')
        ], 'janitor-config');

        $this->publishes([
            __DIR__.'/../resources/config/duster-with-custom-configs.json' => base_path('duster.json'),
            __DIR__.'/../resources/config/phpstan.neon' => base_path('phpstan.neon')
        ], 'janitor-3rd-party-configs');

        $this->publishes([
            __DIR__.'/../resources/workflows/duster-fix.yml' => base_path('duster-fix.yml'),
            __DIR__.'/../resources/workflows/pest-tests.yml' => base_path('pest-tests.yml'),
            __DIR__.'/../resources/workflows/static-analysis.yml' => base_path('static-analysis.yml')
        ], 'janitor-github-actions');
    }
}
