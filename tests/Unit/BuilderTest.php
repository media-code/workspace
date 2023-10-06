<?php

/**
 * These tests do cover the Integration Builder on a unit level
 */

use Gedachtegoed\Workspace\Core\Builder;
use Gedachtegoed\Workspace\Exceptions\ConfigNotFoundException;
use Gedachtegoed\Workspace\Exceptions\WorkflowNotFoundException;

it("throws exception when config source file doesn't exist")
    ->expect(fn () => Builder::make()->publishesConfigs([
        'non-existing-path' => 'destination.json',
    ]))
    ->throws(ConfigNotFoundException::class);

it("throws exception when workflow source file doesn't exist")
    ->expect(fn () => Builder::make()->publishesWorkflows([
        'non-existing-path' => 'destination.json',
    ]))
    ->throws(WorkflowNotFoundException::class);
