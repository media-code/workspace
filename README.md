# Workspace

Extendible workspace configurator for Laravel to effortlessly keep linters, fixers, static analysis, CI workflows, editor integrations and more in sync across all your teams & projects

[![codestyle](https://github.com/media-code/workspace/actions/workflows/codestyle.yml/badge.svg)](https://github.com/media-code/workspace/actions/workflows/codestyle.yml)
[![tests](https://github.com/media-code/workspace/actions/workflows/tests.yml/badge.svg)](https://github.com/media-code/workspace/actions/workflows/tests.yml)
[![coverage](https://img.shields.io/codecov/c/github/media-code/workspace?token=ON4MTY8C1B&color=45%2C190%2C65)](https://codecov.io/gh/media-code/workspace)
[![coverage](https://img.shields.io/codecov/c/github/media-code/workspace-core?label=core%20coverage&token=ON4MTY8C1B&color=45%2C190%2C65)](https://codecov.io/gh/media-code/workspace-core)

<!-- [![Packagist Downloads ](https://img.shields.io/packagist/dt/gedachtegoed/workspace?color=45%2C190%2C65)
](https://packagist.org/packages/gedachtegoed/workspace) -->

## Install Workspace in your project

```bash
composer require gedachtegoed/workspace --dev
```

Then run the install command to set up Workspace's configs in your project:

```bash
php artisan workspace:install
```

## Usage

Workspace will add a couple of `artisan` commands to your project to help keep Integrations in sync with upstream changes:

```bash
# Install configured Integrations
php artisan workspace:install

# Updates workspace & Integration dependencies + rebuilds configs
php artisan workspace:update

# Integrates configured Integrations with your editor
php artisan workspace:integrate
```

Workspace ships with opinionated default integrations. These are easy to change & extend, but the recommended way to work with this package is to publish your own [Portable Workspace](#portable-workspaces). This way you have full control of any upstream configuration changes & very customized setups.

The following composer script aliases will be installed by default inside your project:

```bash
# Linting and fixing
composer lint
composer fix

# Static analysis
composer analyze
composer baseline
```

Note that you can forward options and flags to the underlying composer scripts by suffixing the command with `--`. You may pass any options from either [tighten/duster](https://github.com/tighten/duster) for the `lint` and `fix` commands or [phpstan/phpstan](https://phpstan.org/config-reference) for the `analyze` and `baseline` commands.

For example, if you'd only like to fix dirty files you may use

```bash
composer lint -- --dirty
```

**_NOTE:_** If you don't want Workspace to install composer scripts for you, please remove or edit `Aliases::class` in the package config.

## Integrating with your editor

So far we've got composer scripts & CI workflows to run all your linting, fixing & static analysis.

Let's bridge the gap & make sure your IDE seamlessly applies all the same rules.

```bash
php artisan workspace:integrate
```

You will be prompted to either integrate with `vscode` or `intellij` (like phpstorm).

All default integrations come with publishing of workspace plugins & extensions + workspace specific config. This way we can ensure everyone in your team has the same IDE integration as a baseline, which can be tweaked via the global config.

## Keeping rules up-to-date

Linter, fixer and static analysis rules may change over time. Fortunately it's a breeze to update these locally. Simply run:

```bash
php artisan workspace:update
```

**_NOTE:_**
Workspace checks if your working directory is clean (has no uncommitted files) before starting the internal update. This way it is easier to review upstream changes to the published files.

## Overriding default Integrations

Workspace ships with the following default Integrations:

```php
 return [
    EditorDefaults::class,
    PHPCodeSniffer::class,
    PrettierBlade::class,
    PHPCSFixer::class,
    IDEHelper::class,
    Workflows::class,
    Larastan::class,
    Aliases::class,
    TLint::class,
    Pint::class,
];
```

These can be disabled by publishing Workspace's config file

`php artisan vendor:publish --tag=workspace-config`

You can disable any integrations you don't like or extend them with your own implementation. [Check here](https://github.com/media-code/workspace/tree/main/src/Integrations) to see how the default Integrations are implemented for context.

You may add class names your own Integrations inside the config, or you may simply add Integration builders inline

See the snippet below for a usage example using both flavors:

```php
use Gedachtegoed\Workspace\Integrations\EditorDefaults\EditorDefaults;
use Gedachtegoed\Workspace\Core\Builder;

use App\Workspace\MyCustomPrettierIntegration;

return [
    // Ships with Workspace. Can be combined with custom Integrations
    EditorDefaults::class,

    // FQCN to your custom Integration
    MyCustomPrettierIntegration::class,

    // Inlined Integration using the Builder directly
    Builder::make()
        // Register composer dependencies
        ->composerRequireDev('laravel/telescope:^4')
        ->composerUpdate('laravel/telescope')

        // Hook into afterInstall to configure Telescope
        ->afterInstall(function (Command $command) {
            $command->call('telescope:install');
            $command->call('artisan migrate');

            // NOTE: You can use Laravel Prompts in here to make anything interactive
        })

];
```

A comprehensive Builder API reference & guide on making your own Integrations is in the works. Check back soon.

## Portable Workspaces

Workspace ships with opinionated default Integrations setup. We understand your organization or team has very specific needs. That is why it is easy to distribute your own configuration as a package.

We provide a beautiful fluent integration builder API to automate all sorts of tasks, like:

-   Installing & updating composer & npm dependencies
-   Installing & merging composer script aliases
-   Integrating custom linters & fixers with [Duster](https://github.com/tighten/duster)
-   Publishing integration config files
-   Publishing CI workflow files
-   Adding & removing lines from your gitignore files
-   Provide plugins/extensions for vscode & phpstorm
-   Provide workspace config for vscode & phpstorm

Furthermore Workspace Integrations are fully extendible by use of callable hooks. So you can make the install, update & integrate command do pretty much anything you'd like.

Documentation on using your own Portable Workspaces is pending! Stay tuned!

## Roadmap

[Link to roadmap document](https://github.com/media-code/workspace/ROADMAP.md)
