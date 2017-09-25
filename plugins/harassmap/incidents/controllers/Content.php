<?php

namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Models\Content as ContentModel;
use Harassmap\Incidents\Traits\FilterDomain;

class Content extends Controller
{
    use FilterDomain;
    protected $domain_id = 'domain_id';

    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('harassmap.incidents.domain', 'harassmap.incidents.domain', 'harassmap.incidents.domain.content');
    }

    protected function findDomain($id)
    {
        $content = ContentModel::find($id);

        return $content->domain;
    }
}