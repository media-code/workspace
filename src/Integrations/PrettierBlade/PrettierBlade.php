<?php

namespace Gedachtegoed\Janitor\Integrations\PrettierBlade;

use Gedachtegoed\Janitor\Core\Builder;

class PrettierBlade extends Builder
{
    public function __invoke()
    {
        $this
            ->npmInstall([
                'prettier@^3 --save-dev',
                '@shufo/prettier-plugin-blade --save-dev'
            ])
            ->npmUpdate([
                'prettier',
                'prettier-plugin-blade'
            ])

            ->publishesConfigs([
                '.prettierrc.json' => base_path('.prettierrc.json')
            ])

            ->provideDusterLintConfig([
                'Prettier Blade' => [
                    './node_modules/.bin/prettier',
                    '--check',
                    'resources/**/*.blade.php'
                ]
            ])
            ->provideDusterFixConfig([
                'Prettier Blade' => [
                    './node_modules/.bin/prettier',
                    '--write',
                    'resources/**/*.blade.php'
                ]
            ])

            ->provideVscodeRecommendedPlugins('esbenp.prettier-vscode')
            ->provideVscodeAvoidPlugins('shufo.vscode-blade-formatter') // No need for the vscode plugin, use prettier directly
            ->provideVscodeWorkspaceConfig([
                'prettier.enable' => true,
            ]);
    }
}
