<?php namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Traits\FilterDomain;
use Harassmap\Incidents\Models\Assistance as AssistanceModel;

class Assistance extends Controller
{
    use FilterDomain;
    protected $domain_id = 'domain_id';

    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Incidents', 'harassmap.incidents', 'harassmap.incidents.assistance');
    }

    protected function findDomain($id)
    {
        $assistance = AssistanceModel::find($id);

        return $assistance->domain;
    }
}