<?php

namespace Gedachtegoed\Janitor\Integrations\Workflows;

use Gedachtegoed\Janitor\Core\Builder;

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
