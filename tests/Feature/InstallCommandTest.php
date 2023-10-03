<?php

use Gedachtegoed\Workspace\Core\Aggregator;
use Gedachtegoed\Workspace\Core\Builder;
use Illuminate\Support\Facades\Process;

it('installs composer dependencies', function () {
    register(
        Builder::make()->composerRequire('foo/bar:^2'),
        Builder::make()->composerRequire('bar/baz'),
    );

    Process::fake();

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])
        ->assertSuccessful();

    $dependencies = implode(' ', resolve(Aggregator::class)->composerRequire());
    Process::assertRan("composer require {$dependencies} --dev --no-interaction");
});

it('installs composer dev dependencies', function () {
    register(
        Builder::make()->composerRequireDev('foo/bar:^2'),
        Builder::make()->composerRequireDev('bar/baz'),
    );

    Process::fake();

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])
        ->assertSuccessful();

    $dependencies = implode(' ', resolve(Aggregator::class)->composerRequireDev());
    Process::assertRan("composer require {$dependencies} --dev --no-interaction");
});

it('installs npm dependencies', function () {
    register(
        Builder::make()->npmInstall('foo/bar:^2'),
        Builder::make()->npmInstall('bar/baz'),
    );

    Process::fake();

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])
        ->assertSuccessful();

    $dependencies = implode(' ', resolve(Aggregator::class)->npmInstall());
    Process::assertRan("npm install {$dependencies} --save-dev");
});

it('installs npm dev dependencies', function () {
    register(
        Builder::make()->npmInstallDev('foo/bar:^2'),
        Builder::make()->npmInstallDev('bar/baz'),
    );

    Process::fake();

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])
        ->assertSuccessful();

    $dependencies = implode(' ', resolve(Aggregator::class)->npmInstallDev());
    Process::assertRan("npm install {$dependencies} --save-dev");
});
