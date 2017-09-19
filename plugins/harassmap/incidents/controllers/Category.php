<?php

namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Models\Category as CategoryModel;
use Harassmap\Incidents\Traits\FilterDomain;

class Category extends Controller
{
    use FilterDomain;
    protected $domain_id = 'domain_id';

    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Incidents', 'harassmap.incidents', 'harassmap.incidents.categories');
    }

    protected function findDomain($id)
    {
        $category = CategoryModel::find($id);

        return $category->domain;
    }
}