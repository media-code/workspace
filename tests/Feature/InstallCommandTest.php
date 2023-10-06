<?php

use Gedachtegoed\Workspace\Core\Aggregator;
use Gedachtegoed\Workspace\Core\Builder;
use Illuminate\Support\Facades\Process;
use TiMacDonald\CallableFake\CallableFake;

use function Orchestra\Testbench\package_path;

beforeEach(fn () => pugreSkeleton());
beforeEach(fn () => Process::fake());
afterAll(fn () => pugreSkeleton());

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

it('installs composer scripts', function () {
    expectFileContents('composer.json')->json()->scripts->toBeEmpty();

    register(
        Builder::make()->composerScripts(['foo' => './vendor/bin some-command --with-flags']),
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    expectFileContents('composer.json')
        ->json()
        ->scripts
        ->foo->toBe('./vendor/bin some-command --with-flags');
});

it('merges nested composer scripts', function () {
    expectFileContents('composer.json')->json()->scripts->toBeEmpty();

    register(
        Builder::make()->composerScripts(['some-alias' => 'some-command --with-flags']),
        Builder::make()->composerScripts(['post-update-cmd' => ['some-command']]),
        Builder::make()->composerScripts(['post-update-cmd' => ['some-other-command']]),
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    expectFileContents('composer.json')
        ->json()
        ->scripts
        ->toMatchArray([
            'some-alias' => 'some-command --with-flags',
            'post-update-cmd' => [
                'some-command',
                'some-other-command',
            ],
        ]);
});

//--------------------------------------------------------------------------
// Updates gitignore
//--------------------------------------------------------------------------
it('adds lines to gitignore', function () {
    expectFileExists('.gitignore')->toBeFalse();

    register(
        Builder::make()->addToGitignore('foobar')
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    expectFileContents('.gitignore')->toContain('foobar');
});

it('adds lines to gitignore only once when line already present', function () {
    expectFileExists('.gitignore')->toBeFalse();

    register(
        Builder::make()->addToGitignore('foobar'),
        Builder::make()->addToGitignore('foobar')
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    $gitignore = file_get_contents(base_path('.gitignore'));
    expect(substr_count($gitignore, 'foobar'))->toBe(1);
});

it('removes commented lines from gitignore by regex', function () {
    expectFileExists('.gitignore')->toBeFalse();

    file_put_contents(base_path('.gitignore'), '
        # foobar
        #foobar
        foobar
        baz
    ');

    register(
        Builder::make()->removeFromGitignore('foobar')
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    expectFileContents('.gitignore')
        ->not->toContain('foobar')
        ->toContain('baz');
});

//--------------------------------------------------------------------------
// Publishes configs
//--------------------------------------------------------------------------
it('publishes configs', function () {
    expectFileExists('destination.json')->toBeFalse();

    register(
        Builder::make()->publishesConfigs([
            package_path('tests/Stubs/Integration/source-one.json') => 'destination.json',
        ]),
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    expectFileExists('destination.json')->toBeTrue();
});

//--------------------------------------------------------------------------
// Publishes workflows
//--------------------------------------------------------------------------
it('publishes workflows', function () {
    expectFileExists('.github/workflows/workflow.yml')->toBeFalse();

    register(
        Builder::make()->publishesWorkflows([
            package_path('tests/Stubs/Integration/source-one.json') => '.github/workflows/workflow.yml',
        ]),
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => true])->assertSuccessful();

    expectFileExists('.github/workflows/workflow.yml')->toBeTrue();
});

//--------------------------------------------------------------------------
// Invokes hooks
//--------------------------------------------------------------------------
it('invokes beforeInstall hooks', function () {
    $callableOne = new CallableFake;
    $callableTwo = new CallableFake;

    register(
        Builder::make()->beforeInstall($callableOne),
        Builder::make()->beforeInstall($callableTwo),
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    expect($callableOne)->assertTimesInvoked(1);
    expect($callableTwo)->assertTimesInvoked(1);
});

it('invokes afterInstall hooks', function () {
    $callableOne = new CallableFake;
    $callableTwo = new CallableFake;

    register(
        Builder::make()->afterInstall($callableOne),
        Builder::make()->afterInstall($callableTwo),
    );

    $this->artisan('workspace:install', ['--quickly' => true, '--publish-workflows' => false])->assertSuccessful();

    expect($callableOne)->assertTimesInvoked(1);
    expect($callableTwo)->assertTimesInvoked(1);
});
