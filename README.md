# Janitor

## Install Janitor in your project

```bash
composer require gedachtegoed/janitor --dev
```

Then run the install command to set up Janitor's configs in your project:

```bash
php artisan janitor:install
```

**_NOTE:_** You will be prompted to optionally publish 3rd party config files & Github Actions scripts. This is recommended for GDD projects. You may skip this prompt by adding the `--publish-configs` & `--publish-actions` options

## Usage

The following composer script aliases will be automatically installed inside your project:

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

## Keeping rules up-to-date

Linter, fixer and static analysis rules may change over time. Fortunately it's a breeze to update these locally. Simply run:

```bash
php artisan janitor:update
```

You will be asked again to whether you'd like to publish 3rd party configs. Again, this is recommended. But if you'd like to skip the prompt, simply pass the `--publish-configs` option along with the command.

**Important:** If you choose not to publish third party configs you will opt out of any upstream configuration updates and use the underlying tooling as is.

## Github Actions

If you've chosen to install the Github Actions scripts along when installing Janitor there is nothing left to do. Sensible defaults will work with your next Pull request. However you are free to tweak the file's as you like.

If you've skipped this installation step, not to worry; Simply run `php artisan vendor:publish --tag=janitor-3rd-party-configs` to publish the files now.

## Roadmap

**Docs**

- [ ] Rework readme
- [ ] Consider GH Pages docs
- [ ] Add comprehensive guide for extending Janitor
  - [ ] Override locally with custom implementations
  - [ ] Share your custom integrations & config across your organization's projects
  - [ ] Integration Builder API reference

**Refactors**

- [ ] Integrate Janitor with itself -> linting and fixing for the project
- [ ] Add comprehensive test coverage

- [ ] Refactor `Manager` so it intelligently diffs `suggestedPlugins` & `requiredPlugins` with `avoidPlugins`. (when people want to extend it for their own organization this will avoid plugin conflicts)
- [ ] Consider intelligently merging configs instead of overwriting them? (out of scope?)

**Default configs**

- [ ] Customize Linter & fixer configuration according to GDD flavored styleguide specifications (ongoing)
- [ ] Add sensible default workspace configs
  - [ ] vscode
  - [ ] phpstorm
- [ ] Improve default CI config using merge queues
  - [ ] Add info panel in install command explaining required Github config

**Editor integrations**

- [ ] Implement integration command for PhpStorm (waiting on input)
- [ ] Suggest IDE integrations & workspace for code highlighting & in IDE code fixing
  - [x] vscode
    - [x] PHP CS Fixer
    - [x] PHP CodeSniffer
    - [x] Laravel Pint
    - [x] Tlint
    - [x] PHPStan
    - [x] Prettier
  - [ ] phpstorm
    - [ ] PHP CS Fixer
    - [ ] PHP CodeSniffer
    - [ ] Laravel Pint
    - [ ] Tlint
    - [ ] PHPStan
    - [ ] Prettier

### Done

- [x] Integrate ide-helper with auto update via composer hook
- [x] Add prettier blade plugin support
- [x] Add .editorconfig to EditorDefaults integration

- [x] Major architecture refactor -> fluent integration builder
- [x] Refactor `install` command
- [x] Refactor `integrate` command
- [x] Refactor `update` command
