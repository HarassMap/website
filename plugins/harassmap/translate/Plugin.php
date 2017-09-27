<?php namespace Harassmap\Translate;

use Event;
use Harassmap\Translate\Classes\EventRegistry;
use Harassmap\Translate\Models\Message;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function boot()
    {

        Event::listen('cms.page.init', function ($controller, $page) {
            EventRegistry::instance()->setMessageContext($page);
        }, 100);

    }

    /**
     * Register new Twig variables
     * @return array
     */
    public function registerMarkupTags()
    {
        return [
            'filters' => [
                '_' => [$this, 'translateString']
            ]
        ];
    }

    public function translateString($string, $params = [])
    {
        return Message::trans($string, $params);
    }
}
