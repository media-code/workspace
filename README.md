# Janitor

## Install Janitor in your project
``` bash
composer require gedachtegoed/janitor --dev
```

Then publish all config stubs

``` bash
php artisan janitor:install
```

***NOTE:*** You will be prompted to optionally publish 3rd party config files. This is recommended for GDD projects. You may skip this prompt by adding the `--publish-configs` option

## Updating your local config
Linter, fixer and static analysis rules may change over time. Fortunately it's a breeze to update these locally. Simply run:

``` bash
php artisan janitor:update
```
You will be asked again to whether you'd like to publish 3rd party configs. Again, this is recommended. But if you'd like to skip the prompt, simply pass the `--publish-configs` option along with the command.

## Add composer scripts

Add these entries in the scripts secion of your `composer.json` above `post-autoload-dump` hook.

``` json
"lint": "vendor/bin/duster lint",
"fix": "vendor/bin/duster fix"

"analyze": "vendor/bin/phpstan analyse",
"baseline": "vendor/bin/phpstan analyse --generate-baseline",

"test": "php artisan test --stop-on-failure",
"test-parallel": "php artisan test --parallel --stop-on-failure",
"coverage": "php artisan test --coverage"
```
