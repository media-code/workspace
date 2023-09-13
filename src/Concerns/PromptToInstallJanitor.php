<?php

namespace Gedachtegoed\Janitor\Concerns;

use function Laravel\Prompts\confirm;

trait PromptToInstallJanitor
{
    protected function promptToInstallJanitor()
    {
        if(
            file_exists(base_path('vendor/bin/duster')) &&
            file_exists(base_path('duster.json'))
        ) {
            return;
        }

        $installJanitor = confirm(
            label: 'Looks like Janitor is not installed. Would you like to install Janitor?',
            default: true
        );

        if($installJanitor) {
            $this->call('janitor:install');
        }
    }
}
