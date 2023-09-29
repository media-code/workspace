<?php

use Gedachtegoed\Workspace\Core\Aggregator;

test('merges composer require', function () {
    expect(app(Aggregator::class))
        ->toBeTrue();
});
