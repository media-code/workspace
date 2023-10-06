<?php

namespace Gedachtegoed\Workspace\Core\Concerns;

trait UpdatesGitignore
{
    public function addToGitignore(array|string $lines, string $path = null)
    {
        $lines = (array) $lines;

        $path = $path
            ? $path . DIRECTORY_SEPARATOR . '.gitignore'
            : base_path('.gitignore');

        foreach ($lines as $line) {
            // Remove lines if already there (also removes commented lines) before re-adding them
            $this->removeFromGitignore($line);

            $gitignore = file_exists($path)
                ? file_get_contents($path)
                : '';

            $gitignore = $gitignore . PHP_EOL . $line;

            file_put_contents($path, trim($gitignore) . PHP_EOL);
        }
    }

    public function removeFromGitignore(array|string $lines, string $path = null)
    {
        $lines = (array) $lines;

        if (empty($lines)) {
            return;
        }

        $path = $path
            ? $path . DIRECTORY_SEPARATOR . '.gitignore'
            : base_path('.gitignore');

        if (! file_exists($path)) {
            return;
        }

        $gitignore = file_get_contents($path);

        $implodedLines = implode('|', $lines);
        $gitignore = preg_replace("/^.*(?:{$implodedLines}).*$(?:\r\n|\n)?/m", '', $gitignore);

        // Persist
        file_put_contents($path, trim($gitignore) . PHP_EOL);
    }
}
