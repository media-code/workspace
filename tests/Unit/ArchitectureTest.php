<?php

//--------------------------------------------------------------------------
// Portable Integration sanity checks
//--------------------------------------------------------------------------
test("doesn't use final classes")
    ->expect('Gedachtegoed\\Workspace\\Core')
    ->classes()->not->toBeFinal();

test('Concerns namespace only contains traits')
    ->expect('Core\\Concerns')->toBeTraits()
    ->expect('Commands\\Concerns')->toBeTraits();
