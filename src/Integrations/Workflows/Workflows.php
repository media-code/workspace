<?php

namespace Gedachtegoed\Janitor\Integrations\Workflows;

use Gedachtegoed\Janitor\Core\Builder;

class Workflows extends Builder
{
    public function __invoke()
    {
        $this->publishesWorkflows([
            'duster-fix.yml' => '.github/workflows/duster-fix.yml',
            'pest-tests.yml' => '.github/workflows/pest-tests.yml',
            'static-analysis.yml' => '.github/workflows/static-analysis.yml'
        ]);
    }
}
