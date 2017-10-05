<?php namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Models\Logo;
use Harassmap\Incidents\Traits\FilterDomain;

class Logos extends Controller
{
    use FilterDomain;
    protected $domain_id = 'domain_id';

    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Incidents', 'harassmap.incidents.domain', 'harassmap.incidents.domain.logos');
    }

    protected function findDomain($id)
    {
        $content = Logo::find($id);

        return $content->domain;
    }
}