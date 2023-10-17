---
nav_order: 5
---

# API Reference

When building your own Portable Integrations or overriding Project specific Integrations you have access to the following Fluent Builder methods. The example below showcases all Integration features to your exposal and how you'd structure your Integration class.

{: .note }

> Any of the Builder methods accept either single value or an array of values & may be invoked more than once.

```php
use Gedachtegoed\Workspace\Core\Builder;

class PrettierBlade extends Builder
{
    public function __invoke()
    {
        $this
            //--------------------------------------------------------------------------
            // Package managers
            //--------------------------------------------------------------------------
            ->composerRequireDev(array|string $dependencies)
            ->composerRequire(array|string $dependencies)
            ->composerUpdate(array|string $dependencies)
            ->composerScripts(array|string $scripts)

            ->npmInstallDev(array|string $dependencies)
            ->npmInstall(array|string $dependencies)
            ->npmUpdate(array|string $dependencies)

            //--------------------------------------------------------------------------
            // Duster
            //--------------------------------------------------------------------------
            ->provideDusterLintConfig(array $config)
            ->provideDusterFixConfig(array $config)

            //--------------------------------------------------------------------------
            // Configs and workflows
            //--------------------------------------------------------------------------
            ->publishesConfigs(array $config)
            ->publishesWorkflows(array $config)

            //--------------------------------------------------------------------------
            // Gitignore
            //--------------------------------------------------------------------------
            ->addToGitignore(string|array $line)
            ->removeFromGitignore(string|array $line)

            //--------------------------------------------------------------------------
            // Visual Studio Code integrations
            //--------------------------------------------------------------------------
            ->provideVscodeWorkspaceConfig(string|array $line)
            ->provideVscodeRecommendedPlugins(string|array $plugin)
            ->provideVscodeAvoidPlugins(string|array $plugin)

            //--------------------------------------------------------------------------
            // PhpStorm integrations
            //--------------------------------------------------------------------------
            ->providePhpStormWorkspaceConfig(string|array $line)
            ->providePhpStormRequiredPlugins(string|array $plugin)
            ->providePhpStormSuggestedPlugins(string|array $plugin)

            //--------------------------------------------------------------------------
            // Lifecycle Hooks
            //--------------------------------------------------------------------------
            ->beforeInstall(callable $callback)
            ->afterInstall(callable $callback)

            ->beforeUpdate(callable $callback)
            ->afterUpdate(callable $callback)

            ->beforeIntegration(callable $callback)
            ->afterIntegration(callable $callback)
    }
}
```
