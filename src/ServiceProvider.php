<?php

namespace Gedachtegoed\Janitor;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->publishConfigs();
        $this->registerCommandAliasses();
    }

    public function registerCommandAliasses()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\Install::class,
                Commands\Update::class,
            ]);
        }
    }

    protected function publishConfigs()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/duster.json' => base_path('duster.json'),
        ], 'janitor-config');

        $this->publishes([
            __DIR__ . '/../resources/config/.php-cs-fixer.dist.php' => base_path('.php-cs-fixer.dist.php'),
            __DIR__ . '/../resources/config/duster-with-custom-configs.json' => base_path('duster.json'),
            __DIR__ . '/../resources/config/.phpcs.xml.dist' => base_path('.phpcs.xml.dist'),
            __DIR__ . '/../resources/config/phpstan.neon' => base_path('phpstan.neon'),
            __DIR__ . '/../resources/config/tlint.json' => base_path('tlint.json'),
            __DIR__ . '/../resources/config/pint.json' => base_path('pint.json'),
        ], 'janitor-3rd-party-configs');

        $this->publishes([
            __DIR__ . '/../resources/workflows/static-analysis.yml' => base_path('static-analysis.yml'),
            __DIR__ . '/../resources/workflows/duster-fix.yml' => base_path('duster-fix.yml'),
            __DIR__ . '/../resources/workflows/pest-tests.yml' => base_path('pest-tests.yml'),
        ], 'janitor-github-actions');
    }
}
