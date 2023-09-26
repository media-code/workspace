<?php

namespace Gedachtegoed\Janitor\Integrations\PHPCSFixer;

use Gedachtegoed\Janitor\Integrations\Builder;

class Workflows extends Builder
{
    public function __invoke()
    {
        $this->publishesWorkflows([
            'duster-fix.yml' => base_path('.github/workflows/duster-fix.yml'),
            'pest-tests.yml' => base_path('.github/workflows/pest-tests.yml'),
            'static-analysis.yml' => base_path('.github/workflows/static-analysis.yml')
        ]);
    }
}
