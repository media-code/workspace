<?php

namespace Gedachtegoed\Janitor\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\note;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\confirm;
use Gedachtegoed\Janitor\Core\Aggregator;
use Illuminate\Support\Facades\Process;
use Gedachtegoed\Janitor\Commands\Concerns\PromptForOptionWhenMissing;

class Update extends Command
{
    use PromptForOptionWhenMissing;

    protected Aggregator $integrations;

    protected $signature = 'janitor:update
                                {--publish-workflows : When true, Janitor will publish CI Workflows}';

    protected $description = 'Update Janitor';

    public function __construct(Aggregator $integrations)
    {
        parent::__construct();
        $this->integrations = $integrations;
    }

    public function handle()
    {
        if(! $this->promptToContinueWhenWorkspaceHasUncommittedFiles()) {
            return static::FAILURE;
        }

        // Prompt for input if missing
        $publishWorkflows = $this->promptForOptionWhenMissing(
            option: 'publish-workflows',
            label: 'Would you also like to update CI Workflow files?'
        );

        // Before hooks
        foreach($this->integrations->beforeUpdate() as $callback) {
            $callback($this);
        }

        $this->updateJanitor();
        $this->updateComposerDependencies();
        $this->updateNpmDependencies();
        $this->runJanitorInstall($publishWorkflows);

        // After hooks
        foreach($this->integrations->afterUpdate() as $callback) {
            $callback($this);
        }

        // Nice Laravel-style comment for all you geeks out there
        info(<<<TEXT
            /*
            |-----------------------------------------------------------------------------
            | Did you know?
            |-----------------------------------------------------------------------------
            |
            | Janitor is highly extendable, allowing for sharing of custom integrations
            | across various projects. This ensures a consistent setup for linting,
            | fixing, static analysis, and editor configuration for your team.
            |
            | https://github.com/media-code/janitor/docs/portable-integrations
            */
            TEXT);

        warning('Successfully updated Janitor!');
        note('Please manually review all changes');
    }

    protected function promptToContinueWhenWorkspaceHasUncommittedFiles()
    {
        $result = Process::path(base_path())
            ->run('git status --porcelain')
            ->throw();

        // No changes in tracked files
        if($result->output() === '') {
            return true;
        }

        warning('Janitor detected untracked changes in your project');
        note('We recommend stashing or committing your work before updating Janitor' . PHP_EOL . 'This way it\'s easier to review any upsteam configuration changes');

        return confirm(
            label: 'Do you want to continue?',
            default: false
        );
    }

    protected function updateJanitor()
    {
        spin(
            fn () => Process::path(base_path())
                ->run('composer update gedachtegoed/janitor --no-interaction')
                ->throw(),
            'Updating Janitor'
        );
    }

    protected function updateNpmDependencies()
    {
        $commands = implode(' ', $this->integrations->npmUpdate());

        spin(
            fn() => Process::path(base_path())
                ->run("npm update {$commands}")
                ->throw(),
            'Updating NPM dependencies'
        );
    }

    protected function updateComposerDependencies()
    {
        $commands = implode(' ', $this->integrations->npmUpdate());

        spin(
            fn() => Process::path(base_path())
                ->run("composer update {$commands}")
                ->throw(),
            'Updating Composer dependencies'
        );
    }

    protected function runJanitorInstall(bool $publishWorkflows)
    {
        spin(
            fn() => $this->callSilently('janitor:install', [
                '--publish-workflows' => $publishWorkflows,
                '--quickly' => true
            ]), 'Running janitor:install'
        );
    }
}
