<?php

namespace Gedachtegoed\Janitor\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use Gedachtegoed\Janitor\Core\Aggregator;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\spin;

use Gedachtegoed\Janitor\Core\Concerns\UpdatesGitignore;

class Integrate extends Command
{
    use UpdatesGitignore;

    protected Aggregator $integrations;

    protected $signature = 'janitor:integrate
                            {--editor= : The editor you\'d like to integrate with (vscode, phpstorm)}';

    protected $description = 'Integrate Janitor with your favorite IDE';

    public function __construct(Aggregator $integrations)
    {
        parent::__construct();
        $this->integrations = $integrations;
    }

    public function handle()
    {
        $editors = $this->promptForEditorIfMissing();

        // Before hooks
        foreach($this->integrations->beforeIntegration() as $callback) {
            $callback($this);
        }

        if(in_array('vscode', $editors)) $this->integrateVSCode();
        if(in_array('phpstorm', $editors)) $this->integratePhpStorm();

        // After hooks
        foreach($this->integrations->afterIntegration() as $callback) {
            $callback($this);
        }

        // Show informational messages after integration
        if(in_array('vscode', $editors)) $this->postInstallInfoVSCode();
        if(in_array('phpstorm', $editors)) $this->postInstallInfoPhpStorm();
    }

    /*
    |--------------------------------------------------------------------------
    | Visual Studio Code
    |--------------------------------------------------------------------------
    */
    protected function integrateVSCode()
    {
        spin(function() {
            $this->removeFromGitignore('.vscode');
            $this->publishVSCodeWorkspaceConfig();

            sleep(1); // Only for 💅
        }, 'Integrating Visual Studio Code in your project');
    }

    protected function publishVSCodeWorkspaceConfig()
    {
        // Publish extensions.json
        $extensions = (object) [
            'recommendations' => $this->integrations->provideVscodeRecommendedPlugins(),
            'unwantedRecommendations' => $this->integrations->provideVscodeAvoidPlugins()
        ];

        file_put_contents(
            base_path('.vscode/extensions.json'),
            json_encode($extensions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );

        // Publish settings.json
        file_put_contents(
            base_path('.vscode/settings.json'),
            json_encode($this->integrations->provideVscodeWorkspaceConfig(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );
    }

    protected function postInstallInfoVSCode()
    {
        $this->outputComponents()->info('VSCode workspace environment configured!');
        info('Please reload VSCode & install the workspace recommended extensions when prompted');
        info("If the prompt doesn't appear; Open the command pallette [CMD + Shift + P] and select 'Show Recommended Extensions'");
    }

    /*
    |--------------------------------------------------------------------------
    | PhpStorm
    |--------------------------------------------------------------------------
    */
    protected function integratePhpStorm()
    {
        spin(function() {
            $this->removeFromGitignore('.idea');
            $this->publishPhpStormWorkspaceConfig();

            sleep(1); // Only for 💅
        }, 'Integrating PhpStorm in your project');
    }

    protected function publishPhpStormWorkspaceConfig()
    {
        //
    }

    protected function postInstallInfoPhpStorm()
    {
        warning('TODO: PhpStorm integration pending...');

        // $this->outputComponents()->info('PhpStorm workspace environment configured!');
        // info('Please reload PhpStorm & install the workspace recommended & required plugins when prompted');
    }

    /*
    |--------------------------------------------------------------------------
    | Support
    |--------------------------------------------------------------------------
    */
    private function promptForEditorIfMissing()
    {
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
}
