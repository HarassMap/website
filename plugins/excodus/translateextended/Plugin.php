<?php namespace Excodus\TranslateExtended;

use System\Classes\PluginBase;
use URL;

/**
 * Translate Extended Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Translate Extended',
            'description' => 'excodus.translateextended::lang.strings.plugin_desc',
            'author'      => 'Excodus',
            'icon'        => 'icon-language',
            'homepage'    => 'https://github.com/Excodus/oc-translate-extended'
        ];
    }

    /**
     * @var array Plugin dependencies
     */
    public $require = ['RainLab.Translate'];

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Excodus\TranslateExtended\Components\ExtendedLocalePicker' => 'extendedLocalePicker'
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'excodus.translateextended.access_settings' => [
                'tab'   => 'excodus.translateextended::lang.permissions.tab',
                'label' => 'excodus.translateextended::lang.permissions.settings'
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'translateextended' => [
                'label'       => 'excodus.translateextended::lang.strings.settings_label',
                'description' => 'excodus.translateextended::lang.strings.settings_desc',
                'icon'        => 'icon-language',
                'class'       => 'Excodus\TranslateExtended\Models\Settings',
                'order'       => 552,
                'category'    => 'rainlab.translate::lang.plugin.name',
                'permissions' => ['excodus.translateextended.access_settings']
            ]
        ];
    }

//    TODO: allow users to opt-in into extending |app twig filter
//    /**
//     * Lets extend the app filter.
//     * @return array
//     */
//    public function registerMarkupTags()
//    {
//        return [
//            'filters' => [
//                'app' => [$this, 'appFilter']
//            ]
//        ];
//    }
//
//    /**
//     * Extends the classic app filter
//     * @param  string $url
//     * @return string
//     */
//    public function appFilter($url)
//    {
//        return URL::to(Translator::instance()->getLocale() . '/' . $url);
//    }
}
