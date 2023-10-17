---
nav_order: 2
title: Digging deeper
---

## Core concepts

Workspace in it's core is a system to automate setting up integrations in a programmatic manner inside a distributable composer package.

After Workspace is installed, users may upgrade their local configs by running `workspace:update`. This will update to the latest minor version of Workspace (_or a Portable Workspace you've distributed yourself_), update it's Integrations composer & npm dependencies and finally rebuilds all configuration files before it's published to your project.

Using this system it is a sinch keeping all your integrations synchronized between different projects & teams.

The same is true for the `workspace:integrate` command, which will publish files to your projects workspace config directories (.vscode & .idea) for vscode & phpstorm respectively.

## What do you get?

1. Fluent API for configuring Integrations
2. Head-ache free system for syncing upstream Integration changes
3. Easy to extend, override or roll out your own _Portable Workspaces_
4. Manage composer & npm dependencies
5. Publish config files for said Integrations
6. Hook Integrations into [Duster's](https://github.com/tighten/duster) lint & fix contribution points
7. Contribute to composer.json `scripts` section
8. Contributes to your gitignore files (add & remove lines)
9. Contributes CI workflow files (comes with workflows for linting, fixing tests)
10. Contributes Editor defaults so devs can start working with your project immediatly (without configuring vscode for php)
11. Contributes IDE integrations for Visual Studio Code & PhpStorm

Out of the box, Workspace ships with a handfull default Integrations that vary from setting up editor defaults to installing linters & fixers to setting up IDE Helper & installing composer aliases. You can read about what they do [here](media-code.github.io/workspace/default-integrations).

## Running Duster

The default Integrations workspace ships with integrate with [tightenco/duster](https://github.com/tighten/duster).
After running `workspace:install` for the first time you'll see a table in your CLI with all composer aliases:

| alias             | description                                                                                        |
| :---------------- | :------------------------------------------------------------------------------------------------- |
| composer lint     | Lints your code with duster and phpstan including any additional linters configured in duster.json |
| composer fix      | Fixes your code with duster including any additional fixers configured in duster.json              |
| composer analyze  | Runs phpstan separately                                                                            |
| composer baseline | Generate a static analysis baseline                                                                |

{: .note }

> Duster is a Internal Integration & is always enabled as a way for other Integrations to hook into
>
> If you'd like to change the default script aliases please update or override the [Aliases.php](https://github.com/media-code/workspace/blob/main/src/Integrations/Composer/Aliases.php) implementation

One important thing to note is that all Duster's default integrations will run regardless if you have a Workspace Integration disabled. If you want to run Duster with a custom set of linters you need to update the composer alias accordingly.

```json
"lint": 'vendor/bin/duster lint --using="phpstan,tlint,pint"',
```

Alternatively you may also forward options to the composer alias by appending `--` before any flags.

```bash
composer lint -- --dirty --using"pint"
```

## Overriding Integrations

It may be the case that a project specific configuration keeps getting overwritten by running `workspace:update`. In this case you might choose to override the Integration's implementation on a per-project basis.

First you need to publish the config file by running `php artisan vendor:publish --tag="workspace"`

This will publish the following file to your config folder:

```php
return [
    EditorDefaults::class, // .editorconfig, file associations & emmet languages
    PHPCodeSniffer::class,
    PrettierBlade::class,
    PHPCSFixer::class,
    IDEHelper::class, // IDE helper & update hooks in composer.json
    Workflows::class,
    Larastan::class,
    Aliases::class, // Installs composer scripts
    TLint::class,
    Pint::class,
];
```

_Note that Duster is always enabled as a core Integration_

Here, you may disable any integration you don't want to use. After you've removed a Integration from the config, this will not contribute any configurations to the `install`, `update`, or `integrate` commands.

{: .note }

> If you've previously ran `workspace:install` disabled Integration files won't be deleted on `workspace:update`

When overriding the config you can take two approaches.

1. Using your own class based integration
2. Using the Fluent Builder inline

```php
use Gedachtegoed\Workspace\Integrations\EditorDefaults\EditorDefaults;
use Gedachtegoed\Workspace\Core\Builder;

use App\Workspace\MyCustomPrettierIntegration;

return [
    // Ships with Workspace. Can be combined with custom Integrations
    EditorDefaults::class,

    // Approach 1: FQCN to your custom Integration
    MyCustomPrettierIntegration::class,

    // Approach 2: Inlined Integration using the Builder directly.
    // This example swaps Aliases.php for it's own implementation
    Builder::make()
        ->composerScripts([
            'lint' => 'vendor/bin/duster lint',
            'fix' => 'vendor/bin/duster fix',
            'analyze' => 'vendor/bin/phpstan analyse',
            'baseline' => 'vendor/bin/phpstan analyse --generate-baseline',
        ])
        // Hook into the install command. You have full access to the command & Laravel Prompts
        ->afterInstall(function (Command $command) {
            note('Workspace installed composer aliases for your convenience');

            table(
                ['Command', 'Description'],
                [
                    ['composer lint', 'Lints your code with duster and phpstan including any additional linters configured in duster.json'],
                    ['composer fix', 'Fixes your code with duster including any additional fixers configured in duster.json'],
                    ['composer analyze', 'Runs phpstan separately'],
                    ['composer baseline', 'Generate a static analysis baseline'],
                ]
            );
    })
];
```
