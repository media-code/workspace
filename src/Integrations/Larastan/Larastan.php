<?php

namespace Gedachtegoed\Workspace\Integrations\Larastan;

use Gedachtegoed\Workspace\Core\Builder;

class Larastan extends Builder
{
    public function __invoke()
    {
        $this
            ->composerRequireDev('larastan/larastan:^2.0')
            ->composerUpdate('larastan/larastan')

            ->publishesConfigs([
                'phpstan.neon' => 'phpstan.neon',
            ])

            ->provideDusterLintConfig([
                'Larastan' => [
                    './vendor/bin/phpstan',
                    'analyse',
                    '--memory-limit=2G',
                ],
            ])

            ->provideVscodeRecommendedPlugins('SanderRonde.phpstan-vscode')
            ->provideVscodeWorkspaceConfig([
                'phpstan.enabled' => true,
                'phpstan.enableLanguageServer' => true,
                'phpstan.configFile' => 'phpstan.neon,phpstan.neon.dist,${workspaceFolder}/vendor/gedachtegoed/workspace/src/Integrations/Larastan/phpstan.neon',
            ]);
    }
}
