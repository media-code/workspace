<?php

namespace Gedachtegoed\Janitor\Integrations\IDEHelper;

use Illuminate\Console\Command;
use Gedachtegoed\Janitor\Core\Builder;

class IDEHelper extends Builder
{
    public function __invoke()
    {
        $this
            ->composerRequire('barryvdh/laravel-ide-helper --dev')
            ->composerUpdate('barryvdh/laravel-ide-helper')

            ->addToGitignore([
                '_ide_helper.php',
                '.phpstorm.meta.php'
            ])

            ->composerScripts([
                'post-update-cmd' => [
                    "@php artisan ide-helper:generate --ansi --helpers",
                    "@php artisan ide-helper:meta --ansi"
                ]
            ])

            ->afterInstall(function(Command $command) {
                $command->callSilently('ide-helper:generate', [
                    '--ansi', '--helpers',
                ]);

                $command->callSilently('ide-helper:meta', [
                    '--ansi',
                ]);

                $command->outputComponents()->task('Generating helper & meta files');
            });
    }
}
