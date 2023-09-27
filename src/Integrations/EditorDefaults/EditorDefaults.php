<?php

namespace Gedachtegoed\Janitor\Integrations\EditorDefaults;

use Gedachtegoed\Janitor\Core\Builder;

class EditorDefaults extends Builder
{
    public function __invoke() {
        $this
            ->publishesConfigs([
                '.editorconfig' => base_path('.editorconfig')
            ])
            ->provideVscodeWorkspaceConfig([
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
