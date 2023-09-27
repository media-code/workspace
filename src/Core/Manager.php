<?php

namespace Gedachtegoed\Janitor\Core;

use Illuminate\Support\Collection;
use Gedachtegoed\Janitor\Core\Integration;
use Gedachtegoed\Janitor\Integrations\Duster\Duster;
use Symfony\Component\ErrorHandler\Error\UndefinedMethodError;

/* NOTES
 *
 * remove .vscode & .idea directories when calling integration command
 */

 /**
  * Collection methods for flatMapping Integration properties with magic __call() method
  *
  * @package Gedachtegoed\Janitor\Core
  *
  * @method array publishesConfigs()
  * @method array publishesWorkflows()
  *
  * @method array dusterLintConfig()
  * @method array dusterFixConfig()
  *
  * @method array composerScripts()
  * @method array composerRequire()
  * @method array composerUpdate()
  *
  * @method array npmInstall()
  * @method array npmUpdate()
  *
  * @method array addToGitignore()
  * @method array removeFromGitignore()
  *
  * @method array provideVscodeWorkspaceConfig()
  * @method array provideVscodeRecommendedPlugins()
  * @method array provideVscodeAvoidPlugins()
  * @method array providePhpStormWorkspaceConfig()
  * @method array providePhpStormRequiredPlugins()
  * @method array providePhpStormSuggestedPlugins()
  *
  * @method array beforeInstall()
  * @method array afterInstall()
  * @method array beforeUpdate()
  * @method array afterUpdate()
  * @method array beforeIntegration()
  * @method array afterIntegration()
  */
class Manager
{
    const DEFAULT_INTEGRATIONS = [
        Duster::class
    ];

    /** @param Collection<Integration> */
    public readonly Collection $integrations;

    public function __construct()
    {
        $defaultIntegrations =  $this->resolve(static::DEFAULT_INTEGRATIONS);
        $configuredIntegrations =  $this->resolve(config('janitor-integrations'));

        $this->integrations = $defaultIntegrations->merge($configuredIntegrations);
    }

    /**
     * Resolves Integrations by a array of their respective Builder classes
     *
     * @param array $integrations Array of fully qualified classnames
     */
    private function resolve(array $integrations): Collection
    {
        return collect($integrations)->map(function(string $fqcn) {
            $builder = resolve($fqcn);

            call_user_func($builder); // Calls invoke

            return $builder->getIntegration();
        });
    }

    /**
     * Forwards method calls to collect integration properties.
     * Please see class PHPDoc method hints for a list of methods this provides.
     *
     * @param mixed $name
     */
    public function __call(string $name, ?array $arguments): array
    {
        throw_unless(
            property_exists(Integration::class, $name),
            UndefinedMethodError::class,
        );

        return $this->integrations->flatMap->{$name}->toArray();
    }
}