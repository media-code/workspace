<?php

namespace Gedachtegoed\Workspace\Integrations\IDEHelper;

use Gedachtegoed\Workspace\Core\Builder;
use Gedachtegoed\Workspace\Core\Commands\Install;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\spin;

class IDEHelper extends Builder
{
    public function __invoke()
    {
        $this
            ->composerRequireDev('barryvdh/laravel-ide-helper')
            ->composerUpdate('barryvdh/laravel-ide-helper')

            ->addToGitignore([
                '_ide_helper.php',
                '.phpstorm.meta.php',
            ])

            ->composerScripts([
                'post-update-cmd' => [
                    '@php artisan ide-helper:generate --ansi --helpers',
                    '@php artisan ide-helper:meta --ansi',
                ],
            ])

            ->afterInstall(function (Install $command) {
                spin(function () use ($command) {
                    sleep($command::$SLEEP_BETWEEN_STEPS); // Only for 💅

                    // We can't call the ide-helper artisan command from here
                    // Since the install was triggered in the same process
                    // Run it as a process in a new session instead
                    // TODO: Document this caveat
                    Process::run('composer run post-update-cmd')->throw();
                }, 'Generating helper & meta files');
            });
    }
}
