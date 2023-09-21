<?php

namespace Gedachtegoed\Janitor\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\warning;

class Integrate extends Command
{
    protected $signature = 'janitor:integrate
                            {--editor= : The editor you\'d like to integrate with (vscode, phpstorm)}';

    protected $description = 'Integrate Janitor with your favorite IDE';

    public function handle()
    {
        $editors = $this->promptForEditorIfMissing();

        if(in_array('vscode', $editors)) $this->integrateVSCode();
        if(in_array('phpstorm', $editors)) $this->integratePhpStorm();
    }

    private function promptForEditorIfMissing()
    {
        // Fetch editor option
        $editor = $this->option('editor');

        if(in_array($editor, ['vscode', 'phpstorm'])) {
            return $editor;
        }

        // Option not set or invalid, prompt for input
        return multiselect(
            'What IDE are you using?',
            [
                'vscode' => 'Visual Studio Code',
                'phpstorm' => 'PhpStorm',
            ],
            hint: 'Select one or both',
            required: true,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Visual Studio Code
    |--------------------------------------------------------------------------
   */
    protected function integrateVSCode()
    {
        $this->removeVSCodeDirectoryFromGitignore();
        $this->publishVSCodeWorkspaceConfig();

        info('Please reload VSCode & install the workspace recommended extensions when prompted');
        info("If the prompt doesn't appear; Open the command pallette [CMD + Shift + p] and select 'Show Recommended Extensions'");
    }

    protected function removeVSCodeDirectoryFromGitignore()
    {
        $this->components->info("Removing '/.vscode' from gitignore");

        $gitignore = file_get_contents(base_path('.gitignore'));
        $newGitignore = trim(str_replace('/.vscode', '', $gitignore)) . PHP_EOL;
        file_put_contents(base_path('.gitignore'), $newGitignore);
    }

    protected function publishVSCodeWorkspaceConfig()
    {
        $this->call('vendor:publish', [
            '--tag' => 'janitor-vscode-workspace-settings',
            '--force' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PhpStorm
    |--------------------------------------------------------------------------
   */
    protected function integratePhpStorm()
    {
        $this->removeIdeaDirectoryFromGitignore();
        $this->publishPhpStormWorkspaceConfig();
    }
    protected function removeIdeaDirectoryFromGitignore()
        {
            $this->components->info("Removing '/.idea' from gitignore");

            $gitignore = file_get_contents(base_path('.gitignore'));
            $newGitignore = trim(str_replace('/.idea', '', $gitignore)) . PHP_EOL;
            file_put_contents(base_path('.gitignore'), $newGitignore);
        }

        protected function publishPhpStormWorkspaceConfig()
        {
            $this->components->info("Publishing PhpStorm workspace configuration");

            // TODO
            warning('TODO: PhpStorm integration pending...');
        }
}
