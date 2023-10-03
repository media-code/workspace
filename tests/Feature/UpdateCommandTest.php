<?php

use Gedachtegoed\Workspace\Core\Aggregator;
use Gedachtegoed\Workspace\Core\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

beforeEach(fn () => Process::fake());
//--------------------------------------------------------------------------
// Updates dependencies
//--------------------------------------------------------------------------

it('updates internal workspace dependency', function () {
    $this->artisan('workspace:update', ['--publish-workflows' => false])->assertSuccessful();

    Process::assertRan('composer update gedachtegoed/workspace --no-interaction');
});

it('updates composer dependencies', function () {
    register(
        Builder::make()->composerUpdate('foo/bar'),
        Builder::make()->composerUpdate('bar/baz'),
    );

    $this->artisan('workspace:update', ['--publish-workflows' => false])->assertSuccessful();

    $dependencies = implode(' ', resolve(Aggregator::class)->composerUpdate());
    Process::assertRan("composer update {$dependencies}");
});

it('updates npm dependencies', function () {
    register(
        Builder::make()->npmUpdate('foo/bar'),
        Builder::make()->npmUpdate('bar/baz'),
    );

    $this->artisan('workspace:update', ['--publish-workflows' => false])->assertSuccessful();

    $dependencies = implode(' ', resolve(Aggregator::class)->npmUpdate());
    Process::assertRan("npm update {$dependencies}");
});

//--------------------------------------------------------------------------
// Calls workspace:install internally
//--------------------------------------------------------------------------
it('calls workspace:install', function () {
    Artisan::shouldReceive('callSilently')
        ->with('workspace:install')
        ->once();

    $this->artisan('workspace:update', ['--publish-workflows' => false])->assertSuccessful();
})->todo();

//--------------------------------------------------------------------------
// Invokes hooks
//--------------------------------------------------------------------------
it('invokes beforeUpdate hooks')->todo();
it('invokes afterUpdate hooks')->todo();
