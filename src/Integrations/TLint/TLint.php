<?php

namespace Gedachtegoed\Janitor\Integrations\TLint;

use Gedachtegoed\Janitor\Core\Builder;

class TLint extends Builder
{

    public function __invoke()
    {
        $this
            ->composerRequire('tightenco/tlint --dev')
            ->composerUpdate('tightenco/tlint')
            ->publishesConfigs([
                'tlint.json' => base_path('tlint.json')
            ])
            ->provideVscodeRecommendedPlugins('d9705996.tighten-lint')
            ->provideVscodeWorkspaceConfig([
                'tighten-lint.defaultSeverity' => 'error',
                'tighten-lint.exec' => '${workspaceFolder}/vendor/bin/tlint',
            ]);
    }
}
