<?php

namespace Gedachtegoed\Janitor\Integrations\Workflows;

use Gedachtegoed\Janitor\Core\Builder;

class Workflows extends Builder
{
    public function __invoke()
    {
        $this->publishesWorkflows([
            'lint-and-fix.yml' => '.github/workflows/lint-and-fix.yml',
            'tests.yml' => '.github/workflows/test.yml',
        ]);
    }
}
