<?php

namespace Gedachtegoed\Workspace;

use Gedachtegoed\Workspace\Core\Aggregator;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    // public function provides(): array
    // {
    //     return [Aggregator::class];
    // }

    public function boot(): void
    {
        if (! $this->app->environment(['local', 'testing'])) {
            return;
        }

        // $this->app->bind(
        //     Aggregator::class,
        //     fn () => Aggregator::make(config('workspace-integrations'))
        // );

        $this->publishes([
            __DIR__ . '/../config/workspace-integrations.php' => base_path('config/workspace-integrations.php'),
        ], 'workspace-config');

        // $this->registerCommandAliasses();

        // TODO: Disabled for now. Not sure about the added benefit
        // $this->registerIntegrationConfig();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/workspace-integrations.php', 'workspace-integrations');
    }

    // protected function registerCommandAliasses()
    // {
    //     if ($this->app->runningInConsole()) {
    //         $this->commands([
    //             Commands\Integrate::class,
    //             Commands\Install::class,
    //             Commands\Update::class,
    //         ]);
    //     }
    // }

    /**
     * Only used to provide defaults when running vendor:publish outside of the workspace command
     * The Aggregator class modifies this during runtime to provide up to date config files
     * for your configured Integrations
     */
    // protected function registerIntegrationConfig()
    // {
    //     $integrations = $this->app->make(Aggregator::class);

    //     $this->publishes(
    //         $integrations->publishesConfigs(),
    //         'workspace-3rd-party-configs'
    //     );

    //     $this->publishes(
    //         $integrations->publishesWorkflows(),
    //         'workspace-workflows'
    //     );
    // }

    /** Makes protected methd on parent class accessible so we can register config publishing on the fly */
    // public function publishes(array $paths, $groups = null)
    // {
    //     parent::publishes($paths, $groups);
    // }
}
