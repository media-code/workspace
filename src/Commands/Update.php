<?php

namespace Gedachtegoed\Janitor\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use function Laravel\Prompts\spin;

class Update extends Command
{
    protected $signature = 'janitor:update
                                {--publish-configs : When true, Janitor will update the 3rd party config files}
                                {--publish-actions : When true, Janitor will update the Github Actions for CI}';

    protected $description = 'Update Janitor';

    public function handle(): int
    {

        if (! $this->updateJanitor()) {
            return static::FAILURE;
        }

        return $this->call('janitor:install', [
            '--publish-configs' => $this->option('publish-configs'),
            '--publish-actions' => $this->option('publish-actions'),
        ]);
    }

    protected function updateJanitor()
    {
        $this->components->info('Updating Janitor');

        $result = spin(
            fn () => Process::run('composer update gedachtegoed/janitor --no-interaction'),
            'composer update gedachtegoed/janitor --no-interaction'
        );

        if ($result->failed()) {
            $this->error($result->errorOutput());

            return false;
        }

        $this->line($result->output());

        return true;
    }
}
