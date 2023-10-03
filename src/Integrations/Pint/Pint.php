<?php

namespace Gedachtegoed\Workspace\Integrations\Pint;

use Gedachtegoed\Workspace\Core\Builder;

class Pint extends Builder
{
    public function __invoke()
    {
        $this
            ->composerRequireDev('laravel/pint')
            ->composerUpdate('laravel/pint')
            ->publishesConfigs([
                'pint.json' => 'pint.json',
            ])
            ->provideDusterLintConfig([
                // Already included in Duster
            ])
            ->provideDusterFixConfig([
                // Already included in Duster
            ])
            ->provideVscodeRecommendedPlugins('open-southeners.laravel-pint')
            ->provideVscodeWorkspaceConfig([
                'laravel-pint.enable' => true,
            ]);
    }
}
