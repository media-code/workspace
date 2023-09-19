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
        $publishThirdParty = $this->promptForOptionIfMissing(
            option: 'publish-configs',
            label: 'Would you like to publish the 3rd party config files? (recommended)'
        );

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


    protected function promptForOptionIfMissing(string $option, string $label, bool $default = true)
    {
        $value = $this->option($option);

        if($value) {
            return $value;
        }

        return confirm(
            label: $label,
            default: $default
        );
    }
}
