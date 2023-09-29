<?php

namespace Gedachtegoed\Workspace\Integrations\EditorDefaults;

use Gedachtegoed\Workspace\Core\Builder;

class EditorDefaults extends Builder
{
    public function __invoke()
    {
        $this
            ->publishesConfigs([
                '.editorconfig' => '.editorconfig',
            ])
            ->provideVscodeWorkspaceConfig([
                'files.associations' => (object) [
                    '.php_cs' => 'php',
                    '.php_cs.dist' => 'php',
                    '.env*' => 'dotenv',
                ],
                'emmet.includeLanguages' => (object) [
                    'blade' => 'html',
                    'vue-html' => 'html',
                    'vue' => 'html',
                ],
            ]);
    }
}
