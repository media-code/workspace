<?php

/**
 * Some basic integration level tests.
 * Should be added to the Portable Integrations template repository
 */

use Gedachtegoed\Workspace\Core\Aggregator;
use Gedachtegoed\Workspace\Core\Builder;
use Gedachtegoed\Workspace\ServiceProvider;

//--------------------------------------------------------------------------
// Portable Integration sanity checks
//--------------------------------------------------------------------------
test('integrations pass sanity checks')
    ->expectIntegrationNamespace()
    ->toBeClasses()
    ->classes()->toExtend(Builder::class)
    ->classes()->toBeInvokable()
    ->classes()->not->toBeFinal();

test("integrations don't use forbidden globals")
    ->expect(['dd', 'dump', 'ray'])
    ->not->toBeUsedIn(integrationNamespace());

//--------------------------------------------------------------------------
// Configured Integrations (also coveres inline Builders)
//--------------------------------------------------------------------------
test('configurated integrations are registered')
    ->expect(fn () => config('workspace-integrations'))
    ->not->toBeEmpty();

test('configurated integrations are invokable')
    ->expect(fn () => resolve(Aggregator::class))
    ->integrations()->not->toBeEmpty(); // integrations in config('workspace-integrations') are invoked internally

//--------------------------------------------------------------------------
// Custom expectations
//--------------------------------------------------------------------------
function expectIntegrationNamespace()
{
    return expect(integrationNamespace());
}

function integrationNamespace()
{
    return str(ServiceProvider::class)
        ->beforeLast('\\')
        ->append('\\Integrations')
        ->toString();
}
