<?php

use Gedachtegoed\Workspace\Core\Aggregator;
use Gedachtegoed\Workspace\Core\Builder;
use Illuminate\Support\Facades\Process;

beforeEach(fn () => Process::fake());

//--------------------------------------------------------------------------
// Package managers
//--------------------------------------------------------------------------
it('installs composer dependencies', function () {
    register(
        Builder::make()->composerRequire('foo/bar:^2'),
        Builder::make()->composerRequire('bar/baz'),
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    $dependencies = implode(' ', resolve(Aggregator::class)->composerRequire());
    Process::assertRan("composer require {$dependencies} --no-interaction");
});

it('installs composer dev dependencies', function () {
    register(
        Builder::make()->composerRequireDev('foo/bar:^2'),
        Builder::make()->composerRequireDev('bar/baz'),
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    $dependencies = implode(' ', resolve(Aggregator::class)->composerRequireDev());
    Process::assertRan("composer require {$dependencies} --dev --no-interaction");
});

it('installs npm dependencies', function () {
    register(
        Builder::make()->npmInstall('foo/bar:^2'),
        Builder::make()->npmInstall('bar/baz'),
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    $dependencies = implode(' ', resolve(Aggregator::class)->npmInstall());
    Process::assertRan("npm install {$dependencies}");
});

it('installs npm dev dependencies', function () {
    register(
        Builder::make()->npmInstallDev('foo/bar:^2'),
        Builder::make()->npmInstallDev('bar/baz'),
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    $dependencies = implode(' ', resolve(Aggregator::class)->npmInstallDev());
    Process::assertRan("npm install {$dependencies} --save-dev");
});

it('installs composer scripts')->todo();
it('merges nested composer scripts')->todo();

//--------------------------------------------------------------------------
// Updates gitignore
//--------------------------------------------------------------------------
it('adds lines to gitignore')->todo();
it('adds lines to gitignore only once when line already present')->todo();
it('removes commented lines from gitignore by regex')->todo();

//--------------------------------------------------------------------------
// Publishes configs
//--------------------------------------------------------------------------
it('publishes configs')->todo();

//--------------------------------------------------------------------------
// Publishes workflows
//--------------------------------------------------------------------------
it('publishes workflows')->todo();

//--------------------------------------------------------------------------
// Integrates with vscode
//--------------------------------------------------------------------------
it('removes .vscode from gitignore')->todo();
it('publishes vscode recommended extensions')->todo();
it('publishes vscode unwanted extensions')->todo();
it('publishes vscode workspace config')->todo();

//--------------------------------------------------------------------------
// Integrates with phpstorm
//--------------------------------------------------------------------------
it('removes .idea from gitignore')->todo();
it('publishes phpstorm required plugins')->todo();
it('publishes phpstorm suggested plugins')->todo();
it('publishes phpstorm workspace config')->todo();

//--------------------------------------------------------------------------
// Invokes hooks
//--------------------------------------------------------------------------
it('invokes beforeInstall hooks')->todo();
it('invokes afterInstall hooks')->todo();
