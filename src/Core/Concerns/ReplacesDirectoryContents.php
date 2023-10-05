<?php

namespace Gedachtegoed\Workspace\Core\Concerns;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait ReplacesDirectoryContents
{
    public function replaceDirectoryContents($source, $destination)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }

        return "Contents of {$source} copied to {$destination} successfully.";
    }
}
