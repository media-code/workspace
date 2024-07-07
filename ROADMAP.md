**Docs**

-   [ ] Rework readme
-   [ ] Add comprehensive guide for extending Workspace
    -   [ ] Override locally with custom implementations
    -   [ ] Share your custom integrations & config across your organization's projects
    -   [ ] Integration Builder API reference

**Default configs**

-   [ ] Customize Linter & fixer configuration according to GDD flavored styleguide specifications (ongoing)
-   [ ] Add sensible default workspace configs
    -   [x] vscode
    -   [ ] phpstorm
-   [ ] Improve default CI config using merge queues
    -   [ ] Add info panel in install command explaining required Github config

**Editor integrations**

-   [ ] Implement integration command for PhpStorm (waiting on input)
-   [ ] Suggest IDE integrations & workspace for code highlighting & in IDE code fixing
    -   [x] vscode
        -   [x] PHP CS Fixer
        -   [x] PHP CodeSniffer
        -   [x] Laravel Pint
        -   [x] Tlint
        -   [x] PHPStan
        -   [x] Prettier
    -   [ ] phpstorm
        -   [ ] PHP CS Fixer
        -   [ ] PHP CodeSniffer
        -   [ ] Laravel Pint
        -   [ ] Tlint
        -   [ ] PHPStan
        -   [ ] Prettier

### Done

-   [x] Integrate ide-helper with auto update via composer hook
-   [x] Add prettier blade plugin support
-   [x] Add .editorconfig to EditorDefaults integration

-   [x] Major architecture refactor -> fluent integration builder
-   [x] Refactor `install` command
-   [x] Refactor `integrate` command
-   [x] Refactor `update` command

-   [x] Integrate Duster -> linting and fixing for the project
-   [x] Add comprehensive test coverage
