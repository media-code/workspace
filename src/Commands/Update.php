<?php

namespace Gedachtegoed\Workspace\Commands;

use Gedachtegoed\Workspace\Commands\Concerns\PromptForOptionWhenMissing;
use Gedachtegoed\Workspace\Core\Aggregator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

class Update extends Command
{
    use PromptForOptionWhenMissing;

    protected Aggregator $integrations;

    protected $signature = 'workspace:update
                                {--publish-workflows : When true, Workspace will publish CI Workflows}';

    protected $description = 'Update Workspace';

    public function __construct(Aggregator $integrations)
    {
        parent::__construct();
        $this->integrations = $integrations;
    }

    public function handle()
    {
        if (! $this->promptToContinueWhenWorkspaceHasUncommittedFiles()) {
            return static::FAILURE;
        }

        // Prompt for input if missing
        $publishWorkflows = $this->promptForOptionWhenMissing(
            option: 'publish-workflows',
            label: 'Would you also like to update CI Workflow files?'
        );

        // Before hooks
        foreach ($this->integrations->beforeUpdate() as $callback) {
            $callback($this);
        }

        $this->updateWorkspace();
        $this->updateComposerDependencies();
        $this->updateNpmDependencies();
        $this->runWorkspaceInstall($publishWorkflows);

        // After hooks
        foreach ($this->integrations->afterUpdate() as $callback) {
            $callback($this);
        }

        // Nice Laravel-style comment for all you geeks out there
        info(<<<'TEXT'
            /*
            |-----------------------------------------------------------------------------
            | Did you know?
            |-----------------------------------------------------------------------------
            |
            | Workspace is highly extendable, allowing for sharing of custom integrations
            | across various projects. This ensures a consistent setup for linting,
            | fixing, static analysis, and editor configuration for your team.
            |
            | https://github.com/media-code/workspace/docs/portable-workspace
            */
            TEXT);

        warning('Successfully updated Workspace!');
        note('Please manually review all changes');
        // TODO: Notify this command only bumps workspace package by minor version. Bumping a whole version requires manual composer update
    }

    protected function promptToContinueWhenWorkspaceHasUncommittedFiles()
    {
        $result = Process::path(base_path())
            ->run('git status --porcelain')
            ->throw();

        // No changes in tracked files
        if ($result->output() === '') {
            return true;
        }

        warning('Workspace detected untracked changes in your project');
        note('We recommend stashing or committing your work before updating your workspace' . PHP_EOL . 'This way it\'s easier to review any upsteam configuration changes');

        return confirm(
            label: 'Do you want to continue?',
            default: false
        );
    }

    protected function updateWorkspace()
    {
        spin(
            fn () => Process::path(base_path())
                ->run('composer update gedachtegoed/workspace --no-interaction')
                ->throw(),
            'Updating Workspace'
        );
    }

    protected function updateNpmDependencies()
    {
        $commands = implode(' ', $this->integrations->npmUpdate());

        spin(
            fn () => Process::path(base_path())
                ->run("npm update {$commands}")
                ->throw(),
            'Updating NPM dependencies'
        );
    }

    protected function updateComposerDependencies()
    {
        $commands = implode(' ', $this->integrations->npmUpdate());

        spin(
            fn () => Process::path(base_path())
                ->run("composer update {$commands}")
                ->throw(),
            'Updating Composer dependencies'
        );
    }

    protected function runWorkspaceInstall(bool $publishWorkflows)
    {
        spin(
            fn () => $this->callSilently('workspace:install', [
                '--publish-workflows' => $publishWorkflows,
                '--quickly' => true,
            ]), 'Running workspace:install'
        );
    }
}
