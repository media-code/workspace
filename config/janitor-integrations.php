<?php

use Gedachtegoed\Janitor\Integrations\Pint\Pint;
use Gedachtegoed\Janitor\Integrations\Duster\TLint;
use Gedachtegoed\Janitor\Integrations\Duster\Duster;
use Gedachtegoed\Janitor\Integrations\Larastan\Larastan;
use Gedachtegoed\Janitor\Integrations\PHPCSFixer\PHPCSFixer;
use Gedachtegoed\Janitor\Integrations\PHPCSFixer\PrettierBlade;
use Gedachtegoed\Janitor\Integrations\PHPCSFixer\PHPCodeSniffer;
use Gedachtegoed\Janitor\Integrations\EditorDefaults\EditorDefaults;

return [
    Duster::class, // Move this to Core install command. Must always be installed for other integrations to work
    EditorDefaults::class,
    PHPCodeSniffer::class,
    PrettierBlade::class,
    PHPCSFixer::class,
    Larastan::class,
    TLint::class,
    Pint::class,
];
