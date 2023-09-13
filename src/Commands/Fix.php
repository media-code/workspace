<?php

namespace Gedachtegoed\Janitor\Commands;

use Gedachtegoed\Janitor\Concerns\PromptToInstallJanitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class Fix extends Command
{
    use PromptToInstallJanitor;

    protected $signature = "janitor:fix";

    protected $description = 'Fix your code with Janitor';

    public function handle(): void
    {
        $this->promptToInstallJanitor();

        Process::run("vendor/bin/duster fix");
    }
}
