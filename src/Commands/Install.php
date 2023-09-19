<?php

namespace Gedachtegoed\Janitor\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\confirm;

class Install extends Command
{
    protected $signature = "janitor:install
                            {--publish-configs : When true, Janitor will publish the 3rd party config files}
                            {--publish-actions : When true, Janitor will publish the Github Actions for CI}";

    protected $description = 'Install Janitor';

    public function handle()
    {
        $this->publishConfigs();
        $this->publishActions();
        $this->installComposerScripts();
    }

    protected function publishConfigs()
    {
        // Prompt for input if missing
        $publishThirdParty = $this->promptForOptionIfMissing(
            option: 'publish-configs',
            label: 'Would you like to publish the 3rd party config files? (recommended)'
        );


        // Publish configs
        match ($publishThirdParty) {
            true => $this->call('vendor:publish', [
                '--tag' => 'janitor-3rd-party-configs',
                '--force' => true
            ]),

            default => $this->call('vendor:publish', [
                '--tag' => 'janitor-config',
                '--force' => true
            ])
        };
    }

    protected function publishActions()
    {
        // Prompt for input if missing
        $publicGithubActions = $this->promptForOptionIfMissing(
            option: 'publish-actions',
            label: 'Would you like to publish Github Actions files? (recommended)'
        );

        // Publish Actions
        if(! $publicGithubActions) {
            return;
        }

        $this->call('vendor:publish', [
            '--tag' => 'janitor-github-actions',
            '--force' => true
        ]);
    }


    protected function installComposerScripts()
    {
        $this->components->info('Installing composer scripts');

        $composer = json_decode(file_get_contents(base_path('composer.json')));
        $janitorScripts = json_decode(file_get_contents(__DIR__ . './../../resources/config/composer-scripts.json'));
        $currentScripts = $composer->scripts ?? (object) [];

        data_set(
            target: $composer,
            key: 'scripts',
            value: [...(array) $janitorScripts, ...(array) $currentScripts],
            overwrite: true
        );

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );

        $this->table(
            'Command',
            array_map(fn($alias) => ["composer $alias"], array_keys((array) $janitorScripts))
        );

    }

    protected function promptForOptionIfMissing(string $option, string $label, bool $default = true)
    {
        $value = $this->option($option);

        if($value) {
            return $value;
        }

        return confirm(
            label: $label,
            default: $default
        );
    }
}
