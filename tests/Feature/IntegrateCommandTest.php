<?php

use Illuminate\Support\Facades\Process;

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
it('invokes beforeIntegrate hooks')->todo();
it('invokes afterIntegrate hooks')->todo();
