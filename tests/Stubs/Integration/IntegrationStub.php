<?php

namespace Gedachtegoed\Workspace\Tests\Stubs\Integration;

use Gedachtegoed\Workspace\Core\Builder;

class IntegrationStub extends Builder
{
    public function __invoke()
    {
        $this->publishesConfigs([
            'source-one.json' => 'destination.json',
        ]);

        $this->publishesWorkflows([
            'source-one.json' => 'destination.json',
        ]);
    }
}
