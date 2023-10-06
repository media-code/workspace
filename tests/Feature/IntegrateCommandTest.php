<?php

use Gedachtegoed\Workspace\Core\Builder;
use Illuminate\Support\Facades\Process;
use TiMacDonald\CallableFake\CallableFake;

beforeEach(fn () => pugreSkeleton());
beforeEach(fn () => Process::fake());
afterAll(fn () => pugreSkeleton());

//--------------------------------------------------------------------------
// Integrates with vscode
//--------------------------------------------------------------------------
it('removes .vscode from gitignore', function () {
    // Assert .gitignore doesn't exist
    expectFileExists('.gitignore')->toBeFalse();
    // Add .vscode to gitignore
    gitignore()->addToGitignore('.vscode');
    // Assert that it's there
    expect(file_get_contents(base_path('.gitignore')))
        ->toContain('.vscode');

    $this->artisan('workspace:integrate', ['--editor' => 'vscode'])->assertSuccessful();

    // Assert string is removed from .gitignore
    expectFileContents('.gitignore')
        ->not->toContain('.vscode');
});

it('publishes vscode recommended extensions', function () {
    // Assert extentions.json doesn't exist
    expectFileExists('.vscode/extentions.json')->toBeFalse();

    register(
        Builder::make()->provideVscodeRecommendedPlugins('foo'),
    );

    $this->artisan('workspace:integrate', ['--editor' => 'vscode'])->assertSuccessful();

    // Assert string is present in extensions.json
    expectFileContents('.vscode/extensions.json')
        ->json()
        ->recommendations
        ->toContain('foo');
});

it('publishes vscode unwanted extensions', function () {
    // Assert extentions.json doesn't exist
    expectFileExists('.vscode/extentions.json')->toBeFalse();

    register(
        Builder::make()->provideVscodeAvoidPlugins('foo'),
    );

    $this->artisan('workspace:integrate', ['--editor' => 'vscode'])->assertSuccessful();

    // Assert string is present in extensions.json
    expectFileContents('.vscode/extensions.json')
        ->json()
        ->unwantedRecommendations
        ->toContain('foo');
});

it('publishes vscode workspace config', function () {
    // Assert settings.json doesn't exist
    expectFileExists('.vscode/settings.json')->toBeFalse();

    register(
        Builder::make()->provideVscodeWorkspaceConfig([
            'foobar.enabled' => true,
        ]),
    );

    $this->artisan('workspace:integrate', ['--editor' => 'vscode'])->assertSuccessful();

    // Assert string is present in settings.json
    expectFileContents('.vscode/settings.json')
        ->json()
        ->{'foobar.enabled'}
        ->toBeTrue();
});

//--------------------------------------------------------------------------
// Integrates with phpstorm
//--------------------------------------------------------------------------
it('removes .idea from gitignore', function () {
    // Assert .gitignore doesn't exist
    expectFileExists('.gitignore')->toBeFalse();
    // Add .idea to gitignore
    gitignore()->addToGitignore('.idea');
    // Assert that it's there
    expect(file_get_contents(base_path('.gitignore')))
        ->toContain('.idea');

    $this->artisan('workspace:integrate', ['--editor' => 'phpstorm'])->assertSuccessful();

    // Assert string is removed from .gitignore
    expectFileContents('.gitignore')
        ->not->toContain('.idea');
});

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

    $this->artisan('workspace:integrate', ['--editor' => 'vscode', '--quickly' => true])->assertSuccessful();

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

    $this->artisan('workspace:integrate', ['--editor' => 'vscode', '--quickly' => true])->assertSuccessful();

    expect($callableOne)->assertTimesInvoked(1);
    expect($callableTwo)->assertTimesInvoked(1);
});
