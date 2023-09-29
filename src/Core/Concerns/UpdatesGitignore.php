<?php

namespace Gedachtegoed\Janitor\Core\Concerns;

trait UpdatesGitignore
{
    protected function addToGitignore(array|string $lines, string $path = null)
    {
        $lines = (array) $lines;

        // Remove lines if already there (also removes commented lines) so we get a
        $this->removeFromGitignore($lines);

        $path = $path
            ? $path . DIRECTORY_SEPARATOR . '.gitignore'
            : base_path('.gitignore');

        $gitignore = file_exists($path)
            ? file_get_contents($path)
            : '';

        foreach ($lines as $line) {
            $gitignore = $gitignore . PHP_EOL . $line;
        }

        file_put_contents($path, trim($gitignore) . PHP_EOL);
    }

    protected function removeFromGitignore(array|string $lines, string $path = null)
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
