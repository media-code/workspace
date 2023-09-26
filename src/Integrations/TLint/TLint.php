<?php

namespace Gedachtegoed\Janitor\Integrations\Duster;

use Illuminate\Console\Command;
use Gedachtegoed\Janitor\Integrations\Builder;
use function Laravel\Prompts\table;

class TLint extends Builder
{

    public function __invoke()
    {
        $this
            ->composerRequire('tightenco/tlint --dev')
            ->composerUpdate('tightenco/tlint')
            ->publishesConfigs([
                'tlint.json' => base_dir('tlint.json')
            ])
            ->provideVscodeRecommendedPlugins('d9705996.tighten-lint')
            ->provideVscodeWorkspaceConfig([
                'tighten-lint.defaultSeverity' => 'error',
                'tighten-lint.exec' => '${workspaceFolder}/vendor/bin/tlint',
            ]);
    }
}
