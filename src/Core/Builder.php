<?php

namespace Gedachtegoed\Janitor\Core;

use Illuminate\Console\Command;

abstract class Builder {

    protected Integration $integration;

    abstract public function __invoke();

    public function __construct()
    {
        $this->integration = new Integration;
    }

    public function getIntegration(): Integration
    {
        return $this->integration;
    }

    //--------------------------------------------------------------------------
    // Configs and workflows
    //--------------------------------------------------------------------------
    public function publishesConfigs(array $configMap): self
    {
        $this->integration->publishesConfigs[] = $configMap;
        return $this;
    }

    public function publishesWorkflows(array $workflowMap): self
    {
        $this->integration->publishesWorkflows[] = $workflowMap;
        return $this;
    }

    //--------------------------------------------------------------------------
    // Duster
    //--------------------------------------------------------------------------
    public function provideDusterLintConfig(array $config): self
    {
        $this->integration->dusterLintConfig[] = $config;
        return $this;
    }

    public function provideDusterFixConfig(array $config): self
    {
        $this->integration->dusterFixConfig[] = $config;
        return $this;
    }

    //--------------------------------------------------------------------------
    // Composer
    //--------------------------------------------------------------------------
    public function composerScripts(array|string $scripts): self
    {
        $this->integration->composerScripts[] = (array) $scripts;
        return $this;
    }

    public function composerRequire(array|string $dependencies): self
    {
        $this->integration->composerRequire[] = (array) $dependencies;
        return $this;
    }

    public function composerUpdate(array|string $dependencies): self
    {
        $this->integration->composerUpdate[] = (array) $dependencies;
        return $this;
    }

    //--------------------------------------------------------------------------
    // NPM
    //--------------------------------------------------------------------------
    public function nodePackageManager(string $executable): self
    {
        $this->integration->nodePackageManager = $executable;
        return $this;
    }

    public function npmInstall(array|string $dependencies): self
    {
        $this->integration->npmInstall[] = (array) $dependencies;
        return $this;
    }

    public function npmUpdate(array|string $dependencies): self
    {
        $this->integration->npmUpdate[] = (array) $dependencies;
        return $this;
    }

    //--------------------------------------------------------------------------
    // Gitignore
    //--------------------------------------------------------------------------
    public function addToGitignore(string|array $line): self
    {
        $this->integration->addToGitignore[] = (array) $line;
        return $this;
    }

    public function removeFromGitignore(string|array $line): self
    {
        $this->integration->removeFromGitignore[] = (array) $line;
        return $this;
    }

    //--------------------------------------------------------------------------
    // Visual Studio Code integrations
    //--------------------------------------------------------------------------
    public function provideVscodeWorkspaceConfig(string|array $line): self
    {
        $this->integration->provideVscodeWorkspaceConfig[] = (array) $line;
        return $this;
    }

    public function provideVscodeRecommendedPlugins(string|array $plugins): self
    {
        $this->integration->provideVscodeRecommendedPlugins[] = (array) $plugins;
        return $this;
    }

    public function provideVscodeAvoidPlugins(string|array $plugins): self
    {
        $this->integration->provideVscodeAvoidPlugins[] = (array) $plugins;
        return $this;
    }

    //--------------------------------------------------------------------------
    // PhpStorm integrations
    //--------------------------------------------------------------------------
    public function providePhpStormWorkspaceConfig(string|array $line): self
    {
        $this->integration->providePhpStormWorkspaceConfig[] = (array) $line;
        return $this;
    }

    public function providePhpStormRequiredPlugins(string|array $plugins): self
    {
        $this->integration->providePhpStormRequiredPlugins[] = (array) $plugins;
        return $this;
    }

    public function providePhpStormSuggestedPlugins(string|array $plugins): self
    {
        $this->integration->providePhpStormSuggestedPlugins[] = (array) $plugins;
        return $this;
    }

    //--------------------------------------------------------------------------
    // Lifecycle Hooks
    //--------------------------------------------------------------------------

    /** @param callable(Command): void */
    public function beforeInstall(Callable $callback): self
    {
        $this->integration->beforeInstall[] = $callback;
        return $this;
    }

    /** @param callable(Command): void */
    public function afterInstall(Callable $callback): self
    {
        $this->integration->afterInstall[] = $callback;
        return $this;
    }

    /** @param callable(Command): void */
    public function beforeUpdate(Callable $callback): self
    {
        $this->integration->beforeUpdate[] = $callback;
        return $this;
    }

    /** @param callable(Command): void */
    public function afterUpdate(Callable $callback): self
    {
        $this->integration->afterInstall[] = $callback;
        return $this;
    }

    /** @param callable(Command): void */
    public function beforeIntegration(Callable $callback): self
    {
        $this->integration->beforeIntegration[] = $callback;
        return $this;
    }

    /** @param callable(Command): void */
    public function afterIntegration(Callable $callback): self
    {
        $this->integration->afterIntegration[] = $callback;
        return $this;
    }
}
