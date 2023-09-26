<?php

namespace Gedachtegoed\Janitor\Integrations\PHPStan;

use Gedachtegoed\Janitor\Core\Builder;

class Larastan extends Builder
{
    public function __invoke()
    {
        $this
            ->composerRequire('friendsofphp/php-cs-fixer --dev')
            ->composerUpdate('friendsofphp/php-cs-fixer')

            ->publishesConfigs([
                'phpstan.neon' => base_dir('phpstan.neon')
            ])

            ->provideDusterLintConfig([
                'Larastan' => [
                    './vendor/bin/phpstan',
                    'analyse',
                    '--memory-limit=2G'
                ],
            ])

            ->provideVscodeRecommendedPlugins('SanderRonde.phpstan-vscode')
            ->provideVscodeWorkspaceConfig([
                'phpstan.enabled' => true,
                'phpstan.enableLanguageServer' => true,
                'phpstan.configFile' => 'phpstan.neon,phpstan.neon.dist,${workspaceFolder}/vendor/gedachtegoed/janitor/src/Integrations/Larastan/phpstan.neon',
            ]);
    }
}
