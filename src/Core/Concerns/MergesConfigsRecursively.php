<?php

namespace Gedachtegoed\Workspace\Core\Concerns;

trait MergesConfigsRecursively
{
    /*
     * Recursively merges two arrays
     * Might be prone to break. Not well tested
     *
     * TODO: Refactor to something better & that doesn't make my eyes bleed
     */
    private function mergeConfigsRecursively(array $left, array $right)
    {
        foreach ($right as $key => $value) {
            if (is_array($value) && isset($left[$key]) && is_array($left[$key])) {
                $merged = array_values(array_unique(array_merge($left[$key], $value)));
                $left[$key] = $this->mergeConfigsRecursively($left[$key], $merged);
            } else {
                $left[$key] = $value;
            }
        }

        return $left;
    }
}
