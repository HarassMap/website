<?php namespace Harassmap\MenuManager;

use Backend;
use Controller;
use System\Classes\PluginBase;

/**
 * MenuManager Plugin Information File
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
            'name' => 'harassmap.menumanager::lang.plugin.name',
            'description' => 'harassmap.menumanager::lang.plugin.description',
            'author' => 'Ben Freke',
            'icon' => 'icon-list-alt',
            'homepage' => 'https://github.com/benfreke/oc-menumanager-plugin',
        ];
    }

    /**
     * Create the navigation items for this plugin
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'menumanager' => [
                'label' => 'harassmap.menumanager::lang.menu.name',
                'url' => Backend::url('harassmap/menumanager/menus'),
                'icon' => 'icon-list-alt',
                'permissions' => ['harassmap.menumanager.*'],
                'order' => 500,
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'harassmap.menumanager.access_menumanager' => [
                'label' => 'harassmap.menumanager::lang.access.label',
                'tab' => 'harassmap.menumanager::lang.plugin.name',
            ],
        ];
    }

    /**
     * Register the front end component
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            '\Harassmap\MenuManager\Components\Menu' => 'menu',
        ];
    }
}
