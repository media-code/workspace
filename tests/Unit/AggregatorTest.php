<?php

/**
 * These tests do a top down integration test from the Aggregator to the integration Builder
 * This way we get quite a bit of coverage while covering the inner workings of the
 * Aggregator in combination with narrow and specific integration features.
 */

use Gedachtegoed\Workspace\Core\Aggregator;
use Gedachtegoed\Workspace\Core\Builder;

//--------------------------------------------------------------------------
// Defaults
//--------------------------------------------------------------------------
it('always registers Duster integration', function () {
    register(); // Empty registered integrations

    expect(new Aggregator)
        ->integrations()
        ->toHaveCount(1)
        // FIXME: Can't reflect on the exact implementation used, so use class attributes instead
        ->composerRequire()
        ->toContain('tightenco/duster');
})->todo('Test concrete implementation used, not class properties');

//--------------------------------------------------------------------------
// Package managers
//--------------------------------------------------------------------------
it('aggregates composer install definitions', function () {
    register(
        Builder::make()->composerRequire('package/one'),
        Builder::make()->composerRequire([
            'package/two',
            'package/three:^2.3',
        ]),
    );

    expect(new Aggregator)
        ->composerRequire()
        ->toContain(
            'package/one',
            'package/two',
            'package/three:^2.3',
        );
});

it('aggregates composer update definitions', function () {
    register(
        Builder::make()->composerUpdate('package/one'),
        Builder::make()->composerUpdate([
            'package/two',
            'package/three',
        ]),
    );

    expect(new Aggregator)
        ->composerUpdate()
        ->toContain(
            'package/one',
            'package/two',
            'package/three',
        );
});

it('aggregates composer script definitions')->todo();

it('aggregates npm install definitions')->todo();

it('aggregates npm update definitions')->todo();

//--------------------------------------------------------------------------
// Configs
//--------------------------------------------------------------------------
it('aggregates config files to be published')->todo();
it('maps configs files relative to the integration path and the project base path')->todo();

it('aggregates workflow files to be published')->todo();
it('maps workflow files relative to the integration path and the project base path')->todo();

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
