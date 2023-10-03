<?php

/**
 * These tests do a top down integration test from the Aggregator to the integration Builder
 * This way we get quite a bit of coverage by covering the inner workings of the
 * Aggregator in combination with narrow and specific integration features.
 */

use Gedachtegoed\Workspace\Core\Aggregator;
use Gedachtegoed\Workspace\Core\Builder;
use Gedachtegoed\Workspace\Tests\Stubs\Integration\IntegrationStub;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

use function Orchestra\Testbench\package_path;

//--------------------------------------------------------------------------
// Defaults
//--------------------------------------------------------------------------

// FIXME: Can't reflect on the exact implementation used, so use class attributes instead
it('always registers Duster integration')
    ->expect(fn () => new Aggregator([]))
    ->integrations()
    ->toHaveCount(1)
    ->composerRequire()
    ->toContain('tightenco/duster');

it('resolves aggregator with configured integrations from the container', function () {
    config(['workspace-integrations' => [
        Builder::make()->composerRequire('package/one'),
    ]]);

    expect(resolve(Aggregator::class))
        ->composerRequire()
        ->toContain(
            'package/one',
        );
});

it('aggregates Duster lint scripts to be installed')
    ->expect(fn () => new Aggregator([
        Builder::make()->provideDusterLintConfig([
            'extra-linter' => ['some-command'],
        ]),
        Builder::make()->provideDusterLintConfig([
            'another-extra-linter' => ['some-command'],
        ]),
    ]))
    ->dusterLintConfig()
    ->toMatchArray([
        'extra-linter' => ['some-command'],
        'another-extra-linter' => ['some-command'],
    ]);

it('aggregates Duster fix scripts to be installed')
    ->expect(fn () => new Aggregator([
        Builder::make()->provideDusterFixConfig([
            'extra-fixer' => ['some-command'],
        ]),
        Builder::make()->provideDusterFixConfig([
            'another-extra-fixer' => ['some-command'],
        ]),
    ]))
    ->dusterFixConfig()
    ->toMatchArray([
        'extra-fixer' => ['some-command'],
        'another-extra-fixer' => ['some-command'],
    ]);

//--------------------------------------------------------------------------
// Package managers
//--------------------------------------------------------------------------
it('aggregates composer install definitions', function () {
    $aggregate = new Aggregator([
        Builder::make()->composerRequire('package/one'),
        Builder::make()->composerRequire([
            'package/two',
            'package/three:^2.3',
        ]),
    ]);

    expect($aggregate)
        ->composerRequire()
        ->toContain(
            'package/one',
            'package/two',
            'package/three:^2.3',
        );
});

it('aggregates composer update definitions', function () {
    $aggregate = new Aggregator([
        Builder::make()->composerUpdate('package/one'),
        Builder::make()->composerUpdate([
            'package/two',
            'package/three',
        ]),
    ]);

    expect($aggregate)
        ->composerUpdate()
        ->toContain(
            'package/one',
            'package/two',
            'package/three',
        );
});

it('aggregates composer script definitions', function () {
    $aggregate = new Aggregator([
        Builder::make()->composerScripts([
            'some-alias' => 'some-command',
        ]),
        Builder::make()->composerScripts([
            'some-other-alias' => 'some-other-command',
        ]),
    ]);

    expect($aggregate)
        ->composerScripts()
        ->toEqual([
            'some-alias' => 'some-command',
            'some-other-alias' => 'some-other-command',
        ]);
});

it('aggregates nested composer script definitions', function () {
    $aggregate = new Aggregator([
        Builder::make()->composerScripts([
            'some-alias' => 'some-command',
        ]),
        Builder::make()->composerScripts([
            'post-update-cmd' => [
                'some-command',
            ],
        ]),
        Builder::make()->composerScripts([
            'post-update-cmd' => [
                'some-other-command',
            ],
        ]),
    ]);

    expect($aggregate)
        ->composerScripts()
        ->toEqual([
            'some-alias' => 'some-command',
            'post-update-cmd' => [
                'some-command',
                'some-other-command',
            ],
        ]);
})->todo('FIXME');

it('aggregates npm install definitions')
    ->expect(fn () => new Aggregator([
        Builder::make()->npmInstall('package/one'),
        Builder::make()->npmInstall([
            'package/two',
            'package/three:^2.3',
        ]),
    ]))
    ->npmInstall()
    ->toContain(
        'package/one',
        'package/two',
        'package/three:^2.3',
    );

it('aggregates npm update definitions', function () {
    $aggregate = new Aggregator([
        Builder::make()->npmUpdate('package/one'),
        Builder::make()->npmUpdate([
            'package/two',
            'package/three:^2.3',
        ]),
    ]);

    expect($aggregate)
        ->npmUpdate()
        ->toContain(
            'package/one',
            'package/two',
            'package/three:^2.3',
        );
});

//--------------------------------------------------------------------------
// Configs
//--------------------------------------------------------------------------
it('aggregates config files to be published', function () {
    $aggregate = new Aggregator([
        Builder::make()->publishesConfigs([package_path('tests/Stubs/Integration/source-one.json') => 'destination-one']),
        Builder::make()->publishesConfigs([package_path('tests/Stubs/Integration/source-two.json') => 'destination-two']),
    ]);

    expect($aggregate)
        ->publishesConfigs()
        ->toHaveCount(3); // Count is 3 in stead of expected 2 because duster comes as a default Integration
});

it('maps configs files relative to the integration class path and the project base path', function () {
    $aggregate = new Aggregator([
        IntegrationStub::class,
    ]);

    expect($aggregate)
        ->publishesConfigs()
        ->toMatchArray([
            realpath('tests/Stubs/Integration') . '/source-one.json' => base_path('destination.json'),
        ]);
});

it('maps configs files relative to inlined integration invokation path and the project base path', function () {
    $aggregate = new Aggregator([
        Builder::make()->publishesConfigs([
            package_path('tests/Stubs/Integration/source-one.json') => 'destination.json',
        ]),
    ]);

    expect($aggregate)
        ->publishesConfigs()
        ->toMatchArray([
            package_path('tests/Stubs/Integration/source-one.json') => base_path('destination.json'),
        ]);
});

// TODO: Move this to BuilderTest
it('throws exception when config source file doesnt exist')
    ->expect(fn () => Builder::make()->publishesConfigs([
        'non-existing-path' => 'destination.json',
    ]))
    ->toThrow(FileNotFoundException::class)
    ->todo('FIXME: Fails but does throw expected exception?');

it('aggregates workflow files to be published', function () {
    $aggregate = new Aggregator([
        Builder::make()->publishesWorkflows([package_path('tests/Stubs/Integration/source-one.json') => 'destination-one']),
        Builder::make()->publishesWorkflows([package_path('tests/Stubs/Integration/source-two.json') => 'destination-two']),
    ]);

    expect($aggregate)
        ->publishesWorkflows()
        ->toHaveCount(2);
});

it('maps workflow files relative to the integration class path and the project base path', function () {
    $aggregate = new Aggregator([
        IntegrationStub::class,
    ]);

    expect($aggregate)
        ->publishesWorkflows()
        ->toMatchArray([
            package_path('tests/Stubs/Integration/source-one.json') => base_path('destination.json'),
        ]);
});

it('maps workflow files relative to inlined integration invokation path and the project base path', function () {
    $aggregate = new Aggregator([
        Builder::make()->publishesWorkflows([
            package_path('tests/Stubs/Integration/source-one.json') => 'destination.json',
        ]),
    ]);

    expect($aggregate)
        ->publishesWorkflows()
        ->toMatchArray([
            package_path('tests/Stubs/Integration/source-one.json') => base_path('destination.json'),
        ]);
});

// TODO: Move this to BuilderTest
it('throws exception when workflow source file doesnt exist')
    ->expect(fn () => Builder::make()->publishesWorkflows([
        'non-existing-path' => 'destination.json',
    ]))
    ->toThrow(FileNotFoundException::class)
    ->todo('FIXME: Fails but does throw expected exception?');

//--------------------------------------------------------------------------
// Gitignore
//--------------------------------------------------------------------------
it('aggregates gitignore lines to be added')
    ->expect(fn () => new Aggregator([
        Builder::make()->addToGitignore('file-one'),
        Builder::make()->addToGitignore([
            'file-two',
            'file-three',
        ]),
    ]))
    ->addToGitignore()
    ->toMatchArray([
        'file-one',
        'file-two',
        'file-three',
    ]);

it('aggregates gitignore lines to be removed')
    ->expect(fn () => new Aggregator([
        Builder::make()->removeFromGitignore('file-one'),
        Builder::make()->removeFromGitignore([
            'file-two',
            'file-three',
        ]),
    ]))
    ->removeFromGitignore()
    ->toMatchArray([
        'file-one',
        'file-two',
        'file-three',
    ]);

//--------------------------------------------------------------------------
// IDE integrations
//--------------------------------------------------------------------------
it('aggregates vscode workspace config definitions')->todo();
it('aggregates vscode reccommended plugin definitions')->todo();
it('aggregates vscode avoid plugin definitions')->todo();
it('intelligently merges vscode reccomended and avoid plugin definitions')->todo();

it('aggregates phpstorm workspace config definitions')->todo();
it('aggregates phpstorm required plugin definitions')->todo();
it('aggregates phpstorm suggested plugin definitions')->todo();
it('aggregates phpstorm disabled plugin definitions')->todo(); // Is this even possible?
it('intelligently merges phpstorm reccomended and avoid plugin definitions')->todo();

//--------------------------------------------------------------------------
// Lifecycle Hooks
//--------------------------------------------------------------------------
it('aggregates beforeInstall hooks')->todo();
it('aggregates afterInstall hooks')->todo();

it('aggregates beforeUpdate hooks')->todo();
it('aggregates afterUpdate hooks')->todo();

it('aggregates beforeIntegrate hooks')->todo();
it('aggregates afterIntegrate hooks')->todo();
