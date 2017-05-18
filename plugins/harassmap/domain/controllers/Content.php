<?php

namespace Harassmap\Domain\Controllers;

use Backend\Classes\Controller;
use BackendAuth;
use BackendMenu;

class Content extends Controller
{
    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Domain', 'harassmap.domain', 'harassmap.domain.content');
    }

    public function listExtendQuery($query)
    {
        $user = BackendAuth::getUser();

        // if the user is a super use then stop here
        if ($user->isSuperUser()) {
            return;
        }

        // TODO: Only do the domain check on certain user groups

        $domains = $user->domains;

        foreach ($domains as $domain) {
            $query->orWhere('domain_id', '=', $domain->id);
        }

    }
}