<?php

namespace Gedachtegoed\Janitor\Integrations\EditorDefaults;

use Gedachtegoed\Janitor\Integrations\Builder;

class EditorDefaults extends Builder
{
    public function __invoke() {
        $this->provideVscodeWorkspaceConfig([
            'files.associations' => (object) [
                '.php_cs' => 'php',
                '.php_cs.dist' => 'php',
                '.env*' => 'dotenv'
            ],
            'emmet.includeLanguages' => (object) [
                'blade' => 'html',
                'vue-html' => 'html',
                'vue' => 'html'
            ],
        ]);
    }
}
