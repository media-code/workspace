<?php

use Gedachtegoed\Janitor\Integrations\Pint\Pint;
use Gedachtegoed\Janitor\Integrations\TLint\TLint;
use Gedachtegoed\Janitor\Integrations\Duster\Duster;
use Gedachtegoed\Janitor\Integrations\Larastan\Larastan;
use Gedachtegoed\Janitor\Integrations\IDEHelper\IDEHelper;
use Gedachtegoed\Janitor\Integrations\Workflows\Workflows;
use Gedachtegoed\Janitor\Integrations\PHPCSFixer\PHPCSFixer;
use Gedachtegoed\Janitor\Integrations\PrettierBlade\PrettierBlade;
use Gedachtegoed\Janitor\Integrations\EditorDefaults\EditorDefaults;
use Gedachtegoed\Janitor\Integrations\PHPCodeSniffer\PHPCodeSniffer;

return [
    Duster::class, // Move this to Core install command. Must always be installed for other integrations to work
    EditorDefaults::class,
    PHPCodeSniffer::class,
    PrettierBlade::class,
    PHPCSFixer::class,
    Workflows::class,
    IDEHelper::class,
    Larastan::class,
    TLint::class,
    Pint::class,
];
