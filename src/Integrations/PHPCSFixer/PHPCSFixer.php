<?php

namespace Gedachtegoed\Workspace\Integrations\PHPCSFixer;

use Gedachtegoed\Workspace\Core\Builder;

class PHPCSFixer extends Builder
{
    public function __invoke()
    {
        $this
            ->composerRequireDev('friendsofphp/php-cs-fixer')
            ->composerUpdate('friendsofphp/php-cs-fixer')
            ->publishesConfigs([
                '.php-cs-fixer.dist.php' => '.php-cs-fixer.dist.php',
            ])

            ->provideVscodeRecommendedPlugins('junstyle.php-cs-fixer')
            ->provideVscodeWorkspaceConfig([
                'php-cs-fixer.executablePath' => '${workspaceFolder}/vendor/bin/php-cs-fixer',
                'php-cs-fixer.config' => '.php-cs-fixer.php;.php-cs-fixer.dist.php;.php_cs;.php_cs.dist;${workspaceFolder}/vendor/gedachtegoed/workspace/resources/config/.php-cs-fixer.dist.php',
                'php-cs-fixer.onsave' => false,
                'php-cs-fixer.allowRisky' => false,
                'php-cs-fixer.pathMode' => 'override',
                'php-cs-fixer.autoFixByBracket' => true,
                'php-cs-fixer.autoFixBySemicolon' => true,
                'php-cs-fixer.formatHtml' => false,
            ])

            ->provideDusterLintConfig([
                // Already included in Duster
            ])
            ->provideDusterFixConfig([
                // Already included in Duster
            ]);
    }
}
