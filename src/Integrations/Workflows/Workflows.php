<?php

namespace Gedachtegoed\Workspace\Integrations\Workflows;

use Gedachtegoed\Workspace\Core\Builder;

class Workflows extends Builder
{
    public function __invoke()
    {
        $this->publishesWorkflows([
            'codestyle.yml' => '.github/workflows/codestyle.yml',
            'tests.yml' => '.github/workflows/test.yml',
        ]);
    }
}
