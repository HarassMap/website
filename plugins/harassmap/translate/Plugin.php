<?php namespace Harassmap\Translate;

use Event;
use Harassmap\Translate\Classes\EventRegistry;
use Harassmap\Translate\Components\ExtendedLocalePicker;
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

    public function registerSettings()
    {
        return [
            'translate' => [
                'label'       => 'harassmap.translate::lang.strings.settings_label',
                'description' => 'harassmap.translate::lang.strings.settings_desc',
                'icon'        => 'icon-language',
                'class'       => 'Harassmap\Translate\Models\Settings',
                'order'       => 552,
                'category'    => 'rainlab.translate::lang.plugin.name',
                'permissions' => ['harassmap.translate.access_settings']
            ]
        ];
    }

    public function registerComponents()
    {
        return [
            ExtendedLocalePicker::class => 'harassmapLocalePicker',
        ];
    }
}
