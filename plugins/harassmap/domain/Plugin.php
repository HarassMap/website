<?php

namespace Harassmap\Domain;

use Harassmap\Domain\Components\ContentBlock;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function registerComponents()
    {
        return [
            ContentBlock::class => 'domainContentBlock'
        ];
    }

    public function registerSettings()
    {
    }
}
