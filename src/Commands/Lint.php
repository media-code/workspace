<?php

namespace Gedachtegoed\Janitor\Commands;

use Gedachtegoed\Janitor\Concerns\PromptToInstallJanitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class Lint extends Command
{
    use PromptToInstallJanitor;

    protected $signature = "janitor:lint";

    protected $description = 'Lint your code with Janitor';

    public function handle(): void
    {
        $this->promptToInstallJanitor();

        $result = Process::run("vendor/bin/duster lint");

        echo $result->output();
        echo $result->errorOutput();
    }
}
