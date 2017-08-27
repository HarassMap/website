<?php namespace Harassmap\Comments\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use BackendAuth;

class Comments extends Controller
{
    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Comments', 'harassmap.comments.menu');
    }

    public function formExtendQuery($query)
    {
        $query->withTrashed();
    }

    /**
     * @var string
     *
     * public $domain_id = 'id;
     */

    public function listExtendQuery($query)
    {
        $user = BackendAuth::getUser();

        // if the user is a super use then stop here
        if ($user->isSuperUser() || $user->hasPermission(['harassmap.incidents.domain.manage_domains'])) {
            return;
        }

        $domains = $user->domains;

        if ($domains->isEmpty()) {
            $query->where('id', '=', -1);
        }

        $domain_ids = [];

        foreach ($domains as $domain) {
            $domain_ids[] = $domain->id;
        }

        $query->whereHas('topic', function ($query) use ($domain_ids) {
            $query->whereHas('incident', function($query) use ($domain_ids) {
                $query->whereIn('domain_id', $domain_ids);
            });
        });


    }
}