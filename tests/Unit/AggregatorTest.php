<?php

/**
 * These tests do a top down integration test from the Aggregator to the integration Builder
 * This way we get quite a bit of coverage by covering the inner workings of the
 * Aggregator in combination with narrow and specific integration features.
 */

use Gedachtegoed\Workspace\Core\Aggregator;
use Gedachtegoed\Workspace\Core\Builder;
use Gedachtegoed\Workspace\Tests\Stubs\Integration\IntegrationStub;

use function Orchestra\Testbench\package_path;

//--------------------------------------------------------------------------
// Defaults
//--------------------------------------------------------------------------

// FIXME: Can't reflect on the exact implementation used, so use class attributes instead
it('always registers Duster integration')
    ->expect(fn () => Aggregator::make([]))
    ->integrations()
    ->toHaveCount(1)
    ->composerRequireDev()
    ->toContain('tightenco/duster');

it('resolves aggregator with configured integrations from the container', function () {
    config(['workspace-integrations' => [
        Builder::make()->composerRequireDev('package/one'),
    ]]);

    expect(resolve(Aggregator::class))
        ->composerRequireDev()
        ->toContain(
            'package/one',
        );
});

it('combines default and configured integrations', function () {
    register(
        Builder::make()->composerRequireDev('foo/bar:^2'),
        Builder::make()->composerRequireDev('bar/baz'),
    );

    expect(resolve(Aggregator::class))
        ->integrations()
        ->toHaveCount(
            2 + count(Aggregator::DEFAULT_INTEGRATIONS)
        );
});

it('aggregates Duster lint scripts to be installed')
    ->expect(fn () => Aggregator::make([
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
    ->expect(fn () => Aggregator::make([
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
    $aggregate = Aggregator::make([
        Builder::make()->composerRequireDev('package/one'),
        Builder::make()->composerRequireDev([
            'package/two',
            'package/three:^2.3',
        ]),
    ]);

    expect($aggregate)
        ->composerRequireDev()
        ->toContain(
            'package/one',
            'package/two',
            'package/three:^2.3',
        );
});

it('aggregates composer update definitions', function () {
    $aggregate = Aggregator::make([
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
    $aggregate = Aggregator::make([
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
    $aggregate = Aggregator::make([
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
});

it('aggregates npm install definitions')
    ->expect(fn () => Aggregator::make([
        Builder::make()->npmInstallDev('package/one'),
        Builder::make()->npmInstallDev([
            'package/two',
            'package/three:^2.3',
        ]),
    ]))
    ->npmInstallDev()
    ->toContain(
        'package/one',
        'package/two',
        'package/three:^2.3',
    );

it('aggregates npm update definitions', function () {
    $aggregate = Aggregator::make([
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
    $aggregate = Aggregator::make([
        Builder::make()->publishesConfigs([package_path('tests/Stubs/Integration/source-one.json') => 'destination-one']),
        Builder::make()->publishesConfigs([package_path('tests/Stubs/Integration/source-two.json') => 'destination-two']),
    ]);

    expect($aggregate)
        ->publishesConfigs()
        ->toHaveCount(3); // Count is 3 in stead of expected 2 because duster comes as a default Integration
});

it('maps configs files relative to the integration class path and the project base path', function () {
    $aggregate = Aggregator::make([
        IntegrationStub::class,
    ]);

    expect($aggregate)
        ->publishesConfigs()
        ->toMatchArray([
            realpath('tests/Stubs/Integration') . '/source-one.json' => base_path('destination.json'),
        ]);
});

it('maps configs files relative to inlined integration invokation path and the project base path', function () {
    $aggregate = Aggregator::make([
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

it('aggregates workflow files to be published', function () {
    $aggregate = Aggregator::make([
        Builder::make()->publishesWorkflows([package_path('tests/Stubs/Integration/source-one.json') => 'destination-one']),
        Builder::make()->publishesWorkflows([package_path('tests/Stubs/Integration/source-two.json') => 'destination-two']),
    ]);

    expect($aggregate)
        ->publishesWorkflows()
        ->toHaveCount(2);
});

it('maps workflow files relative to the integration class path and the project base path', function () {
    $aggregate = Aggregator::make([
        IntegrationStub::class,
    ]);

    expect($aggregate)
        ->publishesWorkflows()
        ->toMatchArray([
            package_path('tests/Stubs/Integration/source-one.json') => base_path('destination.json'),
        ]);
});

it('maps workflow files relative to inlined integration invokation path and the project base path', function () {
    $aggregate = Aggregator::make([
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

//--------------------------------------------------------------------------
// Gitignore
//--------------------------------------------------------------------------
it('aggregates gitignore lines to be added')
    ->expect(fn () => Aggregator::make([
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
    ->expect(fn () => Aggregator::make([
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
it('aggregates vscode workspace config definitions')
    ->expect(fn () => Aggregator::make([
        Builder::make()->provideVscodeWorkspaceConfig(['setting-one.enabled' => true]),
        Builder::make()->provideVscodeWorkspaceConfig(['setting-two.enabled' => true]),
    ]))
    ->provideVscodeWorkspaceConfig()
    ->toMatchArray([
        'setting-one.enabled' => true,
        'setting-two.enabled' => true,
    ]);

it('aggregates vscode reccommended plugin definitions')
    ->expect(fn () => Aggregator::make([
        Builder::make()->provideVscodeRecommendedPlugins('gdd.plugin-one'),
        Builder::make()->provideVscodeRecommendedPlugins('gdd.plugin-two'),
    ]))
    ->provideVscodeRecommendedPlugins()
    ->toMatchArray([
        'gdd.plugin-one',
        'gdd.plugin-two',
    ]);

it('aggregates vscode avoid plugin definitions')
    ->expect(fn () => Aggregator::make([
        Builder::make()->provideVscodeAvoidPlugins('gdd.plugin-one'),
        Builder::make()->provideVscodeAvoidPlugins('gdd.plugin-two'),
    ]))
    ->provideVscodeAvoidPlugins()
    ->toMatchArray([
        'gdd.plugin-one',
        'gdd.plugin-two',
    ]);

it('intelligently merges vscode reccomended and avoid plugin definitions')->todo();

it('aggregates phpstorm workspace config definitions')
    ->expect(fn () => Aggregator::make([
        Builder::make()->providePhpStormWorkspaceConfig(['setting-one.enabled' => true]),
        Builder::make()->providePhpStormWorkspaceConfig(['setting-two.enabled' => true]),
    ]))
    ->providePhpStormWorkspaceConfig()
    ->toMatchArray([
        'setting-one.enabled' => true,
        'setting-two.enabled' => true,
    ]);

it('aggregates phpstorm required plugin definitions')
    ->expect(fn () => Aggregator::make([
        Builder::make()->providePhpStormRequiredPlugins('gdd.plugin-one'),
        Builder::make()->providePhpStormRequiredPlugins('gdd.plugin-two'),
    ]))
    ->providePhpStormRequiredPlugins()
    ->toMatchArray([
        'gdd.plugin-one',
        'gdd.plugin-two',
    ]);

it('aggregates phpstorm suggested plugin definitions')
    ->expect(fn () => Aggregator::make([
        Builder::make()->providePhpStormSuggestedPlugins('gdd.plugin-one'),
        Builder::make()->providePhpStormSuggestedPlugins('gdd.plugin-two'),
    ]))
    ->providePhpStormSuggestedPlugins()
    ->toMatchArray([
        'gdd.plugin-one',
        'gdd.plugin-two',
    ]);

it('aggregates phpstorm disabled plugin definitions')->todo('Not sure if possible');
it('intelligently merges phpstorm reccomended and avoid plugin definitions')->todo();

//--------------------------------------------------------------------------
// Lifecycle Hooks
//--------------------------------------------------------------------------
it('aggregates beforeInstall hooks')
    ->expect(fn () => Aggregator::make([
        Builder::make()->beforeInstall(fn () => null),
        Builder::make()->beforeInstall(fn () => null),
    ]))
    ->beforeInstall()
    ->toHaveCount(2);

it('aggregates afterInstall hooks')
    ->expect(fn () => Aggregator::make([
        Builder::make()->afterInstall(fn () => null),
        Builder::make()->afterInstall(fn () => null),
    ]))
    ->afterInstall()
    ->toHaveCount(2);

it('aggregates beforeUpdate hooks')
    ->expect(fn () => Aggregator::make([
        Builder::make()->beforeUpdate(fn () => null),
        Builder::make()->beforeUpdate(fn () => null),
    ]))
    ->beforeUpdate()
    ->toHaveCount(2);

it('aggregates afterUpdate hooks')
    ->expect(fn () => Aggregator::make([
        Builder::make()->afterUpdate(fn () => null),
        Builder::make()->afterUpdate(fn () => null),
    ]))
    ->afterUpdate()
    ->toHaveCount(2);

it('aggregates beforeIntegrate hooks')
    ->expect(fn () => Aggregator::make([
        Builder::make()->beforeIntegration(fn () => null),
        Builder::make()->beforeIntegration(fn () => null),
    ]))
    ->beforeIntegration()
    ->toHaveCount(2);

it('aggregates afterIntegrate hooks')
    ->expect(fn () => Aggregator::make([
        Builder::make()->afterIntegration(fn () => null),
        Builder::make()->afterIntegration(fn () => null),
    ]))
    ->afterIntegration()
    ->toHaveCount(2);
