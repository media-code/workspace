<?php

/**
 * These tests do a top down integration test from the Aggregator to the integration Builder
 * This way we get quite a bit of coverage by covering the inner workings of the
 * Aggregator in combination with narrow and specific integration features.
 */

use Gedachtegoed\Workspace\Core\Aggregator;
use Gedachtegoed\Workspace\Core\Builder;

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
        Builder::make()->publishesConfigs(['source-file-one' => 'destination-one']),
        Builder::make()->publishesConfigs(['source-file-two' => 'destination-two']),
    ]);

    expect($aggregate)
        ->publishesConfigs()
        ->toHaveCount(3); // Count is 3 in stead of expected 2 because duster comes as a default Integration
});

it('maps configs files relative to the integration path and the project base path', function () {
    dump('foo');
    $aggregate = new Aggregator([
        Builder::make()->publishesConfigs(['source-file-one' => 'destination-one']),
        Builder::make()->publishesConfigs(['source-file-two' => 'destination-two']),
    ]);

    // expect($aggregate)
    //     ->publishesConfigs()
    //     ->toContain([
    //         base_path('destionation-one'),
    //     ]);
});

it('throws exception when config source file doesnt exist');

it('aggregates workflow files to be published')->todo();
it('maps workflow files relative to the integration path and the project base path')->todo();
it('throws exception when workflow source file doesnt exist');

it('aggregates Duster lint scripts to be installed')->todo();
it('aggregates Duster fix scripts to be installed')->todo();

//--------------------------------------------------------------------------
// Gitignore
//--------------------------------------------------------------------------
it('aggregates gitignore lines to be added')->todo();
it('aggregates gitignore lines to be removed')->todo();

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
