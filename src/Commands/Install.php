<?php

namespace Gedachtegoed\Workspace\Commands;

use Gedachtegoed\Workspace\Commands\Concerns\PromptForOptionWhenMissing;
use Gedachtegoed\Workspace\Core\Aggregator;
use Gedachtegoed\Workspace\Core\Concerns\MergesConfigsRecursively;
use Gedachtegoed\Workspace\Core\Concerns\UpdatesGitignore;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use RuntimeException;

use function Laravel\Prompts\spin;

class Install extends Command
{
    use MergesConfigsRecursively;
    use PromptForOptionWhenMissing;
    use UpdatesGitignore;

    public static int $SLEEP_BETWEEN_STEPS = 1;

    protected Aggregator $integrations;

    protected $signature = 'workspace:install
                                {--publish-workflows= : When true, Workspace will publish CI Workflows}
                                {--quickly : By default Workspace will sleep 1 second every short running installation step to provide readable progress spinners. This option disables that}';

    protected $description = 'Install Workspace';

    public function __construct(Aggregator $integrations)
    {
        parent::__construct();
        $this->integrations = $integrations;
    }

    public function handle()
    {
        if ($this->option('quickly')) {
            self::$SLEEP_BETWEEN_STEPS = 0;
        }

        // Prompt for input if missing
        $publishWorkflows = $this->promptForOptionWhenMissing(
            option: 'publish-workflows',
            label: 'Would you also like to publish CI Workflow files? (recommended)'
        );

        // Before hooks
        foreach ($this->integrations->beforeInstall() as $callback) {
            $callback($this);
        }

        $this->installComposerDependencies();
        $this->installNpmDependencies();
        $this->publishConfigs();
        $this->updateGitignore();
        $this->installDusterConfiguration();
        $this->installComposerScripts();

        if ($publishWorkflows) {
            $this->publishWorkflows();
        }

        // After hooks
        foreach ($this->integrations->afterInstall() as $callback) {
            $callback($this);
        }
    }

    protected function installComposerDependencies()
    {
        $commands = implode(' ', $this->integrations->composerRequire());
        dump("composer require {$commands} --dev --no-interaction");
        spin(
            fn () => Process::path(base_path())
                ->run("composer require {$commands} --dev --no-interaction")
                ->throw(),
            'Installing Composer dependencies'
        );
    }

    protected function installNpmDependencies()
    {
        $commands = implode(' ', $this->integrations->npmInstall());

        spin(
            fn () => Process::path(base_path())
                ->run("npm install {$commands} --save-dev")
                ->throw(),
            'Installing NPM dependencies'
        );
    }

    protected function publishConfigs()
    {
        spin(function () {
            sleep(self::$SLEEP_BETWEEN_STEPS); // Only for 💅

            $this->callSilent('vendor:publish', [
                '--tag' => 'workspace-3rd-party-configs',
                '--force' => true,
            ]);
        }, 'Publishing 3rd party configs');
    }

    protected function updateGitignore()
    {
        spin(function () {
            sleep(self::$SLEEP_BETWEEN_STEPS); // Only for 💅

            $this->removeFromGitignore(
                $this->integrations->removeFromGitignore()
            );

            $this->addToGitignore(
                $this->integrations->addToGitignore()
            );
        }, 'Updating .gitignore');
    }

    protected function installDusterConfiguration()
    {
        spin(function () {
            sleep(self::$SLEEP_BETWEEN_STEPS); // Only for 💅

            // Note we assume duster.json is present since the Duster integration is mandatory
            $path = base_path('duster.json');
            $config = json_decode(file_get_contents($path));
            $linters = $this->integrations->dusterLintConfig();
            $fixers = $this->integrations->dusterFixConfig();

            foreach ($linters as $name => $integration) {
                data_set($config, "scripts.lint.{$name}", $integration);
            }

            foreach ($fixers as $name => $integration) {
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
        spin(function () {
            sleep(self::$SLEEP_BETWEEN_STEPS); // Only for 💅

            $composer = json_decode(file_get_contents(base_path('composer.json')));
            $workspaceScripts = $this->integrations->composerScripts();
            $composerScripts = $composer->scripts ?? (object) [];

            throw_unless($composer, RuntimeException::class, "composer.json couldn't be parsed");

            // The mergeConfigsRecursively method might be a bit prone to break
            $merged = $this->mergeConfigsRecursively((array) $composerScripts, (array) $workspaceScripts);

            data_set($composer, 'scripts', $merged, overwrite: true);

            file_put_contents(
                base_path('composer.json'),
                json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
            );
        }, 'Installing Composer script aliases');
    }

    protected function publishWorkflows()
    {
        spin(function () {
            sleep(self::$SLEEP_BETWEEN_STEPS); // Only for 💅

            $this->removeFromGitignore([
                '.github',
            ]);

            $this->callSilent('vendor:publish', [
                '--tag' => 'workspace-workflows',
                '--force' => true,
            ]);
        }, 'Publishing workflow files');
    }
}
