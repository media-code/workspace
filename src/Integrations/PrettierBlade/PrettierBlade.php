<?php

namespace Gedachtegoed\Workspace\Integrations\PrettierBlade;

use Gedachtegoed\Workspace\Core\Builder;

class PrettierBlade extends Builder
{
    public function __invoke()
    {
        $this
            ->npmInstallDev([
                'prettier@^3',
                'prettier-plugin-blade@^2',
                'prettier-plugin-tailwindcss',
            ])
            ->npmUpdate([
                'prettier',
                'prettier-plugin-blade',
                'prettier-plugin-tailwindcss',
            ])

            ->publishesConfigs([
                '.prettierrc.json' => '.prettierrc.json',
                '.blade.format.json' => '.blade.format.json',
            ])

            ->provideDusterLintConfig([
                'Prettier Blade' => [
                    './node_modules/.bin/prettier',
                    '--check',
                    'resources/**/*.blade.php',
                ],
            ])
            ->provideDusterFixConfig([
                'Prettier Blade' => [
                    './node_modules/.bin/prettier',
                    '--write',
                    'resources/**/*.blade.php',
                ],
            ])

            ->provideVscodeRecommendedPlugins('esbenp.prettier-vscode')
            ->provideVscodeAvoidPlugins('shufo.vscode-blade-formatter') // No need for the vscode plugin, use prettier directly
            ->provideVscodeWorkspaceConfig([
                'prettier.enable' => true,
            ]);
    }
}
