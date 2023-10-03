<?php

namespace Gedachtegoed\Workspace\Exceptions;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ConfigNotFoundException extends FileNotFoundException
{
    public function __construct($path)
    {
        parent::__construct("The config file '{$path}' doesn't exist. Source should be relative to the Integration class namespace or relative to where the builder was invoked when using inline Integrations.");
    }
}
