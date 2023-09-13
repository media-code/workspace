<?php

namespace Gedachtegoed\Janitor\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\confirm;

class Install extends Command
{
    protected $signature = "janitor:install
                            {--publish-configs : When true, Janitor will also publish the 3rd party config files}";

    protected $description = 'Install Janitor';

    public function handle(): int
    {
        $publishThirdParty = $this->promptForOptionIfMissing();

        return match ($publishThirdParty) {
            true => $this->call('vendor:publish', [
                '--tag' => 'janitor-3rd-party-configs',
                '--force' => true
            ]),

            default => $this->call('vendor:publish', [
                '--tag' => 'janitor-config',
                '--force' => true
            ])
        };
    }


    protected function promptForOptionIfMissing()
    {
        $publishThirdParty = $this->option('publish-configs');

        if(! $publishThirdParty) {
            $publishThirdParty = confirm(
                label: 'Would you like to publish the 3rd party config files? (recommended)',
                default: true
            );
        }

        return $publishThirdParty;
    }
}
