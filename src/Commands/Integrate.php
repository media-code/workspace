<?php

namespace Gedachtegoed\Janitor\Commands;

use Illuminate\Process\Pool;

use Illuminate\Console\Command;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use Illuminate\Support\Facades\Process;
use function Laravel\Prompts\multiselect;

class Integrate extends Command
{
    protected $signature = 'janitor:integrate
                            {--editor= : The editor you\'d like to integrate with (vscode, phpstorm)}';

    protected $description = 'Integrate Janitor with your favorite IDE';

    public function handle()
    {
        $editors = $this->promptForEditorIfMissing();

        $this->integrateIdeHelper();

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
        $this->publishVSCodeWorkspaceConfig();
        $this->removeVSCodeDirectoryFromGitignore();

        info('Please reload VSCode & install the workspace recommended extensions when prompted');
        info("If the prompt doesn't appear; Open the command pallette [CMD + Shift + p] and select 'Show Recommended Extensions'");
    }

    protected function removeVSCodeDirectoryFromGitignore()
    {
        $gitignore = file_get_contents(base_path('.gitignore'));
        $newGitignore = trim(str_replace('/.vscode', '', $gitignore)) . PHP_EOL;
        file_put_contents(base_path('.gitignore'), $newGitignore);

        $this->components->task("Removing '/.vscode' from gitignore");
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
        $this->publishPhpStormWorkspaceConfig();
        $this->removeIdeaDirectoryFromGitignore();
    }
    protected function removeIdeaDirectoryFromGitignore()
    {
        $gitignore = file_get_contents(base_path('.gitignore'));
        $newGitignore = trim(str_replace('/.idea', '', $gitignore)) . PHP_EOL;
        file_put_contents(base_path('.gitignore'), $newGitignore);

        $this->components->task("Removing '/.idea' from gitignore");
    }

    protected function publishPhpStormWorkspaceConfig()
    {
        $this->components->info("Publishing PhpStorm workspace configuration"); // Remove after vendor publish done

        // TODO
        warning('TODO: PhpStorm integration pending...');
    }

    /*
    |--------------------------------------------------------------------------
    | IDE helper
    |--------------------------------------------------------------------------
    */
    protected function integrateIdeHelper()
    {
        $this->components->info('Integrating IDE helper');
        $this->installIdeHelperComposerHook();
        $this->addIdeHelperFilesToGitignore();
        $this->generateIdeHelperFiles();
    }

    protected function installIdeHelperComposerHook()
    {
        $this->components->task('Installing composer hook');

        $composer = json_decode(file_get_contents(base_path('composer.json')));
        $currentScripts = data_get($composer, 'scripts.post-update-cmd', []);
        $helperScripts = [
            "@php artisan ide-helper:generate --ansi --helpers",
            "@php artisan ide-helper:meta --ansi --helpers"
        ];

        data_set(
            target: $composer,
            key: 'scripts.post-update-cmd',
            value:  array_unique(array_values([...$currentScripts, ...$helperScripts])),
            overwrite: true
        );

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );
    }

    protected function addIdeHelperFilesToGitignore()
    {
        $gitignore = trim(file_get_contents(base_path('.gitignore')));

        // Add helper file
        if(
            ! str_contains($gitignore, '_ide_helper.php') ||
            str_contains($gitignore, '# _ide_helper.php')
        ) {
            $gitignore = $gitignore . PHP_EOL . '_ide_helper.php';
        }

        // Add meta file
        if(
            ! str_contains($gitignore, '.phpstorm.meta.php') ||
            str_contains($gitignore, '# .phpstorm.meta.php')
        ) {
            $gitignore = $gitignore . PHP_EOL . '.phpstorm.meta.php';
        }

        // Persist
        file_put_contents(base_path('.gitignore'), $gitignore. PHP_EOL);

        $this->components->task('Adding helper & meta files to .gitignore');
    }

    protected function generateIdeHelperFiles()
    {
        $this->callSilently('ide-helper:generate', [
            '--ansi', '--helpers',
        ]);

        $this->callSilently('ide-helper:meta', [
            '--ansi',
        ]);

        $this->components->task('Generating helper & meta files');
    }
}
