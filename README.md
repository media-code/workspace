# Janitor

## Install Janitor in your project
``` bash
composer require gedachtegoed/janitor --dev
```

Then publish all config stubs

``` bash
php artisan janitor:install
```

***NOTE:*** You will be prompted to optionally publish 3rd party config files & Github Actions scripts. This is recommended for GDD projects. You may skip this prompt by adding the `--publish-configs` & `--publish-actions` options

The following composer script aliases will be automatically installed inside your project:

``` bash
# Linting and fixing
composer lint
composer fix

# Static analysis
composer analyze
composer baseline
```

## Updating your local config
Linter, fixer and static analysis rules may change over time. Fortunately it's a breeze to update these locally. Simply run:

``` bash
php artisan janitor:update
```
You will be asked again to whether you'd like to publish 3rd party configs. Again, this is recommended. But if you'd like to skip the prompt, simply pass the `--publish-configs` option along with the command.

If you choose not to publish third party configs you will opt out of any upstream configuration updates and use the underlying tooling as is.
