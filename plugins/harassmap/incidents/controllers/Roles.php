<?php namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Models\Role;
use Harassmap\Incidents\Traits\FilterDomain;

class Roles extends Controller
{
    use FilterDomain;
    protected $domain_id = 'domain_id';

    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Incidents', 'harassmap.incidents', 'harassmap.incidents.roles');
    }

    protected function findDomain($id)
    {
        $role = Role::find($id);

        return $role->domain;
    }
}