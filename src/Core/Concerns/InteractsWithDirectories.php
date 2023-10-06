<?php

namespace Gedachtegoed\Workspace\Core\Concerns;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait InteractsWithDirectories
{
    public function replaceDirectory($source, $destination)
    {
        // Clean out old dir
        $this->clearDirectory($destination);

        // Copy source contents
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathname());

                continue;
            }
            copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathname());
        }
    }

    public function clearDirectory($dir)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isLink()) {
                continue;
            }

            if ($file->isDir()) {
                rmdir($file->getRealPath());

                continue;
            }

            unlink($file->getRealPath());
        }
    }
}
