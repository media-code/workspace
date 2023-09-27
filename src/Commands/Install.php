<?php

namespace Gedachtegoed\Janitor\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\note;
use function Laravel\Prompts\confirm;
use Gedachtegoed\Janitor\Core\Manager;
use Illuminate\Support\Facades\Process;
use Gedachtegoed\Janitor\Core\Concerns\UpdatesGitignore;
use RuntimeException;

class Install extends Command
{
    use UpdatesGitignore;

    protected Manager $manager;

    protected $signature = 'janitor:install
                            {--publish-workflows : When true, Janitor will publish CI Workflows}';

    protected $description = 'Install Janitor';

    public function __construct(Manager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    public function handle()
    {
        note("Janitor will install ");

        // Prompt for input if missing
        $publishWorkflows = $this->promptForOptionIfMissing(
            option: 'publish-workflows',
            label: 'Would you also like to publish CI Workflow files? (recommended)'
        );

        // Before hooks
        foreach($this->manager->beforeInstall() as $callback) {
            $callback($this);
        }

        $this->installComposerDependencies();
        $this->installNpmDependencies();
        $this->publishConfigs();
        $this->updateGitignore();
        $this->installDusterConfiguration();
        $this->installComposerScripts();

        if($publishWorkflows) {
            $this->publishWorkflows();
        }

        // After hooks
        foreach($this->manager->afterInstall() as $callback) {
            $callback($this);
        }
    }

    protected function installComposerDependencies()
    {
        $commands = implode(' ', $this->manager->composerRequire());

        spin(
            fn() => Process::path(base_path())
                ->run("composer require {$commands} --dev")
                ->throw(),
            'Installing Composer dependencies'
        );
    }

    protected function installNpmDependencies()
    {
        $commands = implode(' ', $this->manager->npmInstall());

        spin(
            fn() => Process::path(base_path())
                ->run("npm install {$commands} --save-dev")
                ->throw(),
            'Installing NPM dependencies'
        );
    }

    protected function publishConfigs()
    {
        spin(function() {
            sleep(1); // Only for 💅

            $this->callSilent('vendor:publish', [
                '--tag' => 'janitor-3rd-party-configs',
                '--force' => true,
            ]);
        }, 'Publishing 3rd party configs');
    }

    protected function updateGitignore()
    {
        spin(function() {
            sleep(1); // Only for 💅

            $this->removeFromGitignore(
                $this->manager->removeFromGitignore()
            );

            $this->addToGitignore(
                $this->manager->addToGitignore()
            );
        }, 'Updating .gitignore');
    }

    protected function installDusterConfiguration()
    {
        spin(function() {
            sleep(1); // Only for 💅

            // Note we assume duster.json is present since the Duster integration is mandatory
            $path = base_path('duster.json');
            $config = json_decode(file_get_contents($path));
            $linters = $this->manager->dusterLintConfig();
            $fixers = $this->manager->dusterFixConfig();

            foreach($linters as $name => $integration) {
                data_set($config, "scripts.lint.{$name}", $integration);
            }

            foreach($fixers as $name => $integration) {
                data_set($config, "scripts.fix.{$name}", $integration, overwrite: true);
            }

            // Persist
            file_put_contents(
                $path,
                json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
            );
        }, 'Installing integrations in Duster config');
    }

    protected function installComposerScripts()
    {
        spin(function() {
            sleep(1); // Only for 💅

            $composer = json_decode(file_get_contents(base_path('composer.json')));
            $janitorScripts = $this->manager->composerScripts();
            $composerScripts = $composer->scripts ?? (object) [];

            throw_unless($composer, RuntimeException::class, "composer.json couldn't be parsed");

            // The mergeRecursive method might be a bit prone to break
            $merged = $this->mergeRecursive((array) $composerScripts, (array) $janitorScripts);

            data_set($composer, 'scripts', $merged, overwrite: true);

            file_put_contents(
                base_path('composer.json'),
                json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
            );
        }, 'Installing Composer script aliases');
    }

    protected function publishWorkflows()
    {
        spin(function() {
            sleep(1); // Only for 💅

            $this->removeFromGitignore([
                '.github',
            ]);

            $this->callSilent('vendor:publish', [
                '--tag' => 'janitor-workflows',
                '--force' => true,
            ]);

        }, 'Publishing workflow files');
    }

    //--------------------------------------------------------------------------
    // Support
    //--------------------------------------------------------------------------

    protected function promptForOptionIfMissing(string $option, string $label, bool $default = true)
    {
        $value = $this->option($option);

        if ($value) {
            return $value;
        }

        return confirm(
            label: $label,
            default: $default
        );
    }

    /*
     * Not really recursive. Can only go 1 level deep
     * Might be prone to break. Not well tested
     *
     * TODO: Refactor to something better & that doesn't make my eyes bleed
     */
    private function mergeRecursive(array $left, array $right) {
        foreach ($right as $key => $value) {
            if (is_array($value) && isset($left[$key]) && is_array($left[$key])) {
                $left[$key] = array_values(array_unique(array_merge($left[$key], $value)));
            } else {
                $left[$key] = $value;
            }
        }
        return $left;
    }
}
