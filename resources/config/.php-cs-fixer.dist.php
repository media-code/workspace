<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use Gedachtegoed\Janitor\Fixer\ClassNotation\CustomPhpUnitOrderFixer;
use Gedachtegoed\Janitor\Fixer\ClassNotation\CustomControllerOrderFixer;
use Gedachtegoed\Janitor\Fixer\ClassNotation\CustomOrderedClassElementsFixer;

$finder = Finder::create()
    ->notName([
        '*.blade.php',
    ])
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->in(__DIR__);

return (new Config())
    ->setFinder($finder)
    ->setUsingCache(false)
    ->registerCustomFixers([
        new CustomControllerOrderFixer(),
        new CustomOrderedClassElementsFixer(),
        new CustomPhpUnitOrderFixer(),
    ])
    ->setRules([
        'Tighten/custom_controller_order' => true,
        'Tighten/custom_ordered_class_elements' => [
            'order' => [
                'use_trait',
                'case',
                'property_public_static',
                'property_protected_static',
                'property_private_static',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'method:__invoke',
                'method_public_static',
                'method_protected_static',
                'method_private_static',
                'method_public',
                'method_protected',
                'method_private',
                'magic',
            ],
        ],
        'Tighten/custom_phpunit_order' => true,
    ]);
