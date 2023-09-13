<?php

namespace Gedachtegoed\Janitor\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Laravel\Prompts\confirm;


class Install extends Command
{
    protected $signature = "janitor:install
                            {--publish-configs : When true, Janitor will also publish the 3rd party config files}";

    protected $description = 'Install Janitor';

    public function handle(): int
    {
        return match ($this->option('publish-configs')) {
            true => $this->call('vendor:publish', [
                '--tag' => 'janitor-3rd-party-configs'
            ]),

            false => $this->call('vendor:publish', [
                '--tag' => 'janitor-config'
            ])
        };
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        $input->setOption('--publish-configs', confirm(
            label: 'Would you like to publish the 3rd party config files? (recommended)',
            default: $this->option('publish-configs') ?? false
        ));
    }
}
