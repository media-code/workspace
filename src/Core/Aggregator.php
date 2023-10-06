<?php

namespace Gedachtegoed\Workspace\Core;

use Gedachtegoed\Workspace\Core\Concerns\MergesConfigsRecursively;
use Gedachtegoed\Workspace\Integrations\Duster\Duster;
use Gedachtegoed\Workspace\ServiceProvider;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Symfony\Component\ErrorHandler\Error\UndefinedMethodError;

/**
 * Collection methods for flatMapping Integration properties with magic __call() method
 *
 * TODO: Consider renaming methods (and proxied Integration properties). Either consistent in structure or mirroring underlying action name (like composerRequire as opposed to installsComposerDependencies)
 *
 * |--------------------------------------------------------------------------
 * | Configs
 * |--------------------------------------------------------------------------
 *
 * @method array publishesConfigs()
 * @method array publishesWorkflows()
 * @method array dusterLintConfig()
 * @method array dusterFixConfig()
 * |--------------------------------------------------------------------------
 * | Package managers
 * |--------------------------------------------------------------------------
 * @method array composerScripts()
 * @method array composerRequire()
 * @method array composerRequireDev()
 * @method array composerUpdate()
 * @method array npmInstall()
 * @method array npmInstallDev()
 * @method array npmUpdate()
 * |--------------------------------------------------------------------------
 * | Gitignore
 * |--------------------------------------------------------------------------
 * @method array addToGitignore()
 * @method array removeFromGitignore()
 * |--------------------------------------------------------------------------
 * | Integrations
 * |--------------------------------------------------------------------------
 * @method array provideVscodeWorkspaceConfig()
 * @method array provideVscodeRecommendedPlugins()
 * @method array provideVscodeAvoidPlugins()
 * @method array providePhpStormWorkspaceConfig()
 * @method array providePhpStormRequiredPlugins()
 * @method array providePhpStormSuggestedPlugins()
 * |--------------------------------------------------------------------------
 * | Hooks
 * |--------------------------------------------------------------------------
 * @method array beforeInstall()
 * @method array afterInstall()
 * @method array beforeUpdate()
 * @method array afterUpdate()
 * @method array beforeIntegration()
 * @method array afterIntegration()
 */
class Aggregator
{
    use MergesConfigsRecursively;

    const DEFAULT_INTEGRATIONS = [
        Duster::class,
    ];

    /** @var Collection<Integration> */
    protected readonly Collection $integrations;

    /** @param  array<string|Builder>  $integrations */
    public function __construct(array $integrations)
    {
        $defaultIntegrations = $this->resolve(static::DEFAULT_INTEGRATIONS);
        $configuredIntegrations = $this->resolve($integrations);

        $this->integrations = $defaultIntegrations->merge($configuredIntegrations);

        $this->registerIntegrationPublishGroups();
    }

    /**
     * Static constructor
     *
     * @param  array<string|Builder>  $integrations
     */
    public static function make(array $integrations)
    {
        return new self($integrations);
    }

    /**
     * Returns all resolved integrations
     */
    public function integrations(): Collection
    {
        return $this->integrations;
    }

    /**
     * Resolves Integrations by a array of their respective Builder classes
     *
     * @param  array  $integrations Array of fully qualified classnames
     * @return Collection<Integration>
     */
    private function resolve(array $integrations): Collection
    {
        return collect($integrations)->map(function (string|Builder $implementation) {
            // Check if the given implementation resolves to a Builder implementation
            throw_unless(
                $implementation instanceof Builder || is_subclass_of($implementation, Builder::class),
                InvalidArgumentException::class,
                'Workspace configuration must be of type Workspace\\Core\\Builder'
            );

            // Either resolve classname from container or use inlined builder instance from config
            $builder = $implementation instanceof Builder
                ? $implementation
                : resolve($implementation);

            // If the implementation has an invoke method, invoke that automatically
            method_exists($builder, '__invoke')
                ? call_user_func($builder)
                : null;

            return $builder->getIntegration();
        });
    }

    private function registerIntegrationPublishGroups()
    {
        // @phpstan-ignore-next-line
        app()->getProvider(ServiceProvider::class)->publishes(
            $this->publishesConfigs(),
            'workspace-3rd-party-configs'
        );

        // @phpstan-ignore-next-line
        app()->getProvider(ServiceProvider::class)->publishes(
            $this->publishesWorkflows(),
            'workspace-workflows'
        );
    }

    /**
     * Forwards method calls to collect integration properties.
     * Please see class PHPDoc method hints for a list of methods this provides.
     *
     * @param  string  $name property of Integration to aggregate
     */
    public function __call(string $name, ?array $arguments): array
    {
        throw_unless(
            property_exists(Integration::class, $name),
            UndefinedMethodError::class,
        );

        return $this->integrations->flatMap->{$name}->toArray();
    }

    /**
     * Returns aggregated composer script definition
     * Not proxied via magic __call because configs need to be merged recursively
     */
    public function composerScripts(): array
    {
        return $this->integrations->reduce(
            fn ($carry, $integration) => $this->mergeConfigsRecursively($carry, $integration->composerScripts),
            []
        );
    }
}
