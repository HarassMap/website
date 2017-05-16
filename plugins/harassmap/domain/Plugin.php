<?php

namespace Harassmap\Domain;

use Harassmap\Domain\Components\ContentBlock;
use Harassmap\Domain\Components\Domain;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function registerComponents()
    {
        return [
            ContentBlock::class => 'domainContentBlock',
            Domain::class => 'domain'
        ];
    }

    public function registerSettings()
    {
    }
}
