<?php

namespace Gedachtegoed\Janitor\Integrations\IDEHelper;

use Illuminate\Console\Command;
use Gedachtegoed\Janitor\Core\Builder;
use function Laravel\Prompts\spin;

class IDEHelper extends Builder
{
    public function __invoke()
    {
        $this
            ->composerRequire('barryvdh/laravel-ide-helper')
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
                spin(function() use ($command) {
                    sleep(1); // Only for 💅

                    $command->callSilently('ide-helper:generate', [
                        '--ansi', '--helpers',
                    ]);

                    $command->callSilently('ide-helper:meta', [
                        '--ansi',
                    ]);

                }, 'Generating helper & meta files');
            });
    }
}
