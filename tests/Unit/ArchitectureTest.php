<?php

//--------------------------------------------------------------------------
// Forbidden globals
//--------------------------------------------------------------------------
test("integrations don't use forbidden globals")
    ->expect(['dd', 'dump', 'ray'])->not->toBeUsed();

//--------------------------------------------------------------------------
// Portable Integration sanity checks
//--------------------------------------------------------------------------
test("doesn't use final classes")
    ->expect('Gedachtegoed\\Workspace\\Core')
    ->classes()->not->toBeFinal();

test('concerns namespaces only contains traits')
    ->expect('Core\\Concerns')->toBeTraits()
    ->expect('Commands\\Concerns')->toBeTraits();
