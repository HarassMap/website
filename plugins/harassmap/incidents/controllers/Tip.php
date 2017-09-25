<?php

namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Models\Tip as TipModel;
use Harassmap\Incidents\Traits\FilterDomain;

class Tip extends Controller
{
    use FilterDomain;

    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $bodyClass = 'compact-container';

    protected $domain_id = 'domain_id';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('harassmap.incidents.domain', 'harassmap.incidents.domain', 'harassmap.incidents.domain.tips');
    }

    protected function findDomain($id)
    {
        $tip = TipModel::find($id);

        return $tip->domain;
    }
}