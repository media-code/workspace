<?php

namespace Gedachtegoed\Janitor\Integrations\Duster;

use Gedachtegoed\Janitor\Core\Builder;

class Duster extends Builder
{

    public function __invoke()
    {
        $this
            ->composerRequire('tightenco/duster')
            ->composerUpdate('tightenco/duster')
            ->publishesConfigs([
                'duster.json' => 'duster.json'
            ]);
    }
}
