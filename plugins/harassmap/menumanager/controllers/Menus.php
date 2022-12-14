<?php namespace Harassmap\MenuManager\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Traits\FilterDomain;
use Harassmap\MenuManager\Models\Menu;
use Lang;

/**
 * Menus Back-end Controller
 */
class Menus extends Controller
{
    use FilterDomain;
    protected $domain_id = 'domain_id';

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend\Behaviors\ReorderController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public $requiredPermissions = ['harassmap.menumanager.access_menumanager'];

    /**
     * Ensure that by default our edit menu sidebar is active
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Harassmap.MenuManager', 'menumanager', 'edit');

        // Add my assets
        $this->addJs('/plugins/harassmap/menumanager/assets/js/harassmap.menumanager.js');
    }

    protected function findDomain($id)
    {
        $menu = Menu::find($id);

        return $menu->domain;
    }
}
