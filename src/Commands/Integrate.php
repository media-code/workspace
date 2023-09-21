<?php

namespace Gedachtegoed\Janitor\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\select;
use function Laravel\Prompts\note;
use function Laravel\Prompts\info;

class Integrate extends Command
{
    protected $signature = 'janitor:integrate
                            {--editor= : The editor you\'d like to integrate with (vscode, phpstorm)}';

    protected $description = 'Integrate Janitor with your favorite IDE';

    public function handle()
    {
        $editor = $this->promptForEditorIfMissing();

        match($editor) {
            'vscode' => $this->integrateVSCode(),
            'phpstorm' => note('TODO: PhpStorm integration not ready'),
        };
    }

    private function promptForEditorIfMissing()
    {
        // Fetch editor option
        $editor = $this->option('editor');

        if(in_array($editor, ['vscode', 'phpstorm'])) {
            return $editor;
        }

        // Option not set or invalid, prompt for input
        return select(
            'What IDE are you using?',
            [
                'vscode' => 'Visual Studio Code',
                'phpstorm' => 'PhpStorm',
            ]
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

        info('Please reload VSCode & install the workspace reccomended extensions when prompted');
        info("If the prompt doesn't appear; Open the command pallette [CMD + Shift + p] and select 'Show Recommended Extensions'");
    }

    protected function removeVSCodeDirectoryFromGitignore()
    {
        $this->components->info("Removing '/.vscode' from gitignore");

        //
    }

    protected function publishVSCodeWorkspaceConfig()
    {
        $this->components->info("Publishing workspace configuration");
        //
    }

    /*
    |--------------------------------------------------------------------------
    | PhpStorm
    |--------------------------------------------------------------------------
   */
    //
}
