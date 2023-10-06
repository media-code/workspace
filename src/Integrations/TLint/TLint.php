<?php

namespace Gedachtegoed\Workspace\Integrations\TLint;

use Gedachtegoed\Workspace\Core\Builder;

class TLint extends Builder
{
    public function __invoke()
    {
        $this
            ->composerRequireDev('tightenco/tlint')
            ->composerUpdate('tightenco/tlint')
            ->publishesConfigs([
                'tlint.json' => 'tlint.json',
            ])
            ->provideVscodeRecommendedPlugins('d9705996.tighten-lint')
            ->provideVscodeWorkspaceConfig([
                'tighten-lint.defaultSeverity' => 'error',
                'tighten-lint.exec' => '${workspaceFolder}/vendor/bin/tlint',
            ]);
    }
}
