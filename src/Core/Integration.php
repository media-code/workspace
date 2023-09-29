<?php

namespace Gedachtegoed\Janitor\Core;

use Gedachtegoed\Janitor\Commands\Install;
use Gedachtegoed\Janitor\Commands\Integrate;
use Gedachtegoed\Janitor\Commands\Update;

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
    /** @var callable(Install)[]: void */
    public array $beforeInstall = [];

    /** @var callable(Install)[]: void */
    public array $afterInstall = [];

    /** @var callable(Update)[]: void */
    public array $beforeUpdate = [];

    /** @var callable(Update)[]: void */
    public array $afterUpdate = [];

    /** @var callable(Integrate)[]: void */
    public array $beforeIntegration = [];

    /** @var callable(Integrate)[]: void */
    public array $afterIntegration = [];
}
