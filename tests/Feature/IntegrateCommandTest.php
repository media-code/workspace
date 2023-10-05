<?php

use Gedachtegoed\Workspace\Core\Builder;
use Illuminate\Support\Facades\Process;
use TiMacDonald\CallableFake\CallableFake;

// beforeEach(fn () => Process::run('composer purge-skeleton') && Process::fake());
beforeEach(fn () => pugreSkeleton());
beforeEach(fn () => Process::fake());

//--------------------------------------------------------------------------
// Integrates with vscode
//--------------------------------------------------------------------------
it('removes .vscode from gitignore')->todo('testbench does not allow resetting .gitignore. Use unit level test instead');

it('publishes vscode recommended extensions', function () {
    // Assert extentions.json doesn't exist
    // expect(file_exists(base_path('.vscode/extentions.json')))->toBeFalse();

    // register(
    //     Builder::make()->provideVscodeRecommendedPlugins('foo'),
    // );

    // $this->artisan('workspace:integrate', ['--editor' => 'vscode'])->assertSuccessful();

    // // Assert string is present in extensions.json
    // expect(file_get_contents(base_path('.vscode/extentions.json')))->toContain('foo');
})->todo();

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
it('invokes beforeIntegrate hooks', function () {

    $callableOne = new CallableFake;
    $callableTwo = new CallableFake;

    register(
        Builder::make()->afterIntegration($callableOne),
        Builder::make()->afterIntegration($callableTwo),
    );

    $this->artisan('workspace:integrate', ['--editor' => 'vscode'])->assertSuccessful();

    expect($callableOne)->assertTimesInvoked(1);
    expect($callableTwo)->assertTimesInvoked(1);
});

it('invokes afterIntegrate hooks', function () {

    $callableOne = new CallableFake;
    $callableTwo = new CallableFake;

    register(
        Builder::make()->afterIntegration($callableOne),
        Builder::make()->afterIntegration($callableTwo),
    );

    $this->artisan('workspace:integrate', ['--editor' => 'vscode'])->assertSuccessful();

    expect($callableOne)->assertTimesInvoked(1);
    expect($callableTwo)->assertTimesInvoked(1);
});
