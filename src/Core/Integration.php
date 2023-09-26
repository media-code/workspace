<?php

namespace Gedachtegoed\Janitor\Core;

final class Integration
{
    //--------------------------------------------------------------------------
    // Configs
    //--------------------------------------------------------------------------
    public array $publishesConfigs = [];
    public array $publishesWorkflows = [];

    public array $dusterLintConfig = [];
    public array $dusterFixConfig = [];

    //--------------------------------------------------------------------------
    // Package managers
    //--------------------------------------------------------------------------
    public array $composerScripts = [];
    public array $composerRequire = [];
    public array $composerUpdate = [];

    public string $nodePackageManager = 'npm';
    public array $npmInstall = [];
    public array $npmUpdate = [];

    //--------------------------------------------------------------------------
    // Gitignore
    //--------------------------------------------------------------------------
    public array $addToGitignore = [];
    public array $removeFromGitignore = [];

    //--------------------------------------------------------------------------
    // IDE integrations
    //--------------------------------------------------------------------------
    public array $provideVscodeWorkspaceConfig = [];
    public array $provideVscodeRecommendedPlugins = [];
    public array $provideVscodeAvoidPlugins = [];

    public array $providePhpStormWorkspaceConfig = []; // TODO: Implement
    public array $providePhpStormRequiredPlugins = []; // TODO: Implement
    public array $providePhpStormSuggestedPlugins = []; // TODO: Implement

    //--------------------------------------------------------------------------
    // Lifecycle Hooks
    //--------------------------------------------------------------------------
    /** @var callable(Command)[]: void */
    public array $beforeInstall = [];
    /** @var callable(Command)[]: void */
    public array $afterInstall = [];

    /** @var callable(Command)[]: void */
    public array $beforeUpdate = [];
    /** @var callable(Command)[]: void */
    public array $afterUpdate = [];

    /** @var callable(Command)[]: void */
    public array $beforeIntegration = [];
    /** @var callable(Command)[]: void */
    public array $afterIntegration = [];
}
