<?php

use Gedachtegoed\Workspace\Core\Builder;
use Illuminate\Support\Facades\Process;
use TiMacDonald\CallableFake\CallableFake;

beforeEach(fn () => Process::fake());

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
