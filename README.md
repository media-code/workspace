# Janitor



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
