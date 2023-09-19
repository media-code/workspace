<?php

namespace Gedachtegoed\Janitor\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\confirm;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command
{
    protected $signature = "janitor:update
                                {--publish-configs : When true, Janitor will update the 3rd party config files}
                                {--publish-actions : When true, Janitor will update the Github Actions for CI}";

    protected $description = 'Update Janitor';

    public function handle(): int
    {

        if(! $this->updateJanitor()) {
            return static::FAILURE;
        }

        return $this->call('janitor:install', [
            '--publish-configs' => $this->option('publish-configs'),
            '--publish-actions' => $this->option('publish-actions')
        ]);
    }

    protected function updateJanitor()
    {
        // TODO: stream output
        $result = Process::run("composer update gedachtegoed/janitor --no-interaction");

        if($result->failed()) {
            $this->error($result->errorOutput());

            return static::FAILURE;
        }

        return $result->successful();
    }
}
