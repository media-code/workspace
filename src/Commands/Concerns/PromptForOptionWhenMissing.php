<?php

namespace Gedachtegoed\Janitor\Commands\Concerns;

use function Laravel\Prompts\confirm;

trait PromptForOptionWhenMissing
{
    protected function promptForOptionWhenMissing(string $option, string $label, bool $default = true)
    {
        $value = $this->option($option);

        if ($value) {
            return $value;
        }

        return confirm(
            label: $label,
            default: $default
        );
    }
}