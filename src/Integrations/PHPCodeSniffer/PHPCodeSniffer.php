<?php

namespace Gedachtegoed\Workspace\Integrations\PHPCodeSniffer;

use Gedachtegoed\Workspace\Core\Builder;

class PHPCodeSniffer extends Builder
{
    public function __invoke()
    {
        $this
            ->composerRequireDev('squizlabs/php_codesniffer')
            ->composerUpdate('squizlabs/php_codesniffer')
            ->publishesConfigs([
                '.phpcs.xml.dist' => '.phpcs.xml.dist',
            ])
            ->provideDusterLintConfig([
                // Already included in Duster
            ])
            ->provideDusterFixConfig([
                // Already included in Duster
            ])
            ->provideVscodeRecommendedPlugins('wongjn.php-sniffer')
            ->provideVscodeAvoidPlugins('ikappas.phpcs')
            ->provideVscodeWorkspaceConfig([
                'phpSniffer.autoDetect' => false,
                'phpSniffer.executablesFolder' => './vendor/bin',
                'phpSniffer.extraFiles' => [],
                'phpSniffer.onTypeDelay' => 250,
                'phpSniffer.run' => 'onType',
                'phpSniffer.snippetExcludeSniffs' => [],
            ]);
    }
}
