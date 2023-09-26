<?php

namespace Gedachtegoed\Janitor\Integrations\PHPCodeSniffer;

use Gedachtegoed\Janitor\Core\Builder;

class PHPCodeSniffer extends Builder
{
    public function __invoke()
    {
        $this
            ->composerRequire('friendsofphp/php-cs-fixer --dev')
            ->composerUpdate('friendsofphp/php-cs-fixer')
            ->publishesConfigs([
                '.phpcs.xml.dist' => base_path('.phpcs.xml.dist')
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
                'phpSniffer.autoDetect' =>  false,
                'phpSniffer.executablesFolder' =>  './vendor/bin',
                'phpSniffer.extraFiles' =>  [],
                'phpSniffer.onTypeDelay' =>  250,
                'phpSniffer.run' =>  'onType',
                'phpSniffer.snippetExcludeSniffs' =>  [],
            ]);
    }
}
