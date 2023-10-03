<?php

namespace Gedachtegoed\Workspace\Commands\Concerns;

use function Laravel\Prompts\confirm;

trait PromptForOptionWhenMissing
{
    protected function promptForOptionWhenMissing(string $option, string $label, bool $default = true)
    {
        $value = $this->option($option);

        if ($value !== null) {
            return $value;
        }

        return confirm(
            label: $label,
            default: $default
        );
    }
}
