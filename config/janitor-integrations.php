<?php

use Gedachtegoed\Janitor\Integrations\Composer\Aliases;
use Gedachtegoed\Janitor\Integrations\EditorDefaults\EditorDefaults;
use Gedachtegoed\Janitor\Integrations\IDEHelper\IDEHelper;
use Gedachtegoed\Janitor\Integrations\Larastan\Larastan;
use Gedachtegoed\Janitor\Integrations\PHPCodeSniffer\PHPCodeSniffer;
use Gedachtegoed\Janitor\Integrations\PHPCSFixer\PHPCSFixer;
use Gedachtegoed\Janitor\Integrations\Pint\Pint;
use Gedachtegoed\Janitor\Integrations\PrettierBlade\PrettierBlade;
use Gedachtegoed\Janitor\Integrations\TLint\TLint;
use Gedachtegoed\Janitor\Integrations\Workflows\Workflows;

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
