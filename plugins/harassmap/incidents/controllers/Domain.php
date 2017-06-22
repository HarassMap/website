<?php

namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
use BackendAuth;
use BackendMenu;
use Harassmap\Incidents\Models\Domain as DomainModel;
use Harassmap\Incidents\Traits\FilterDomain;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Domain extends Controller
{
    use FilterDomain;

    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Incidents', 'harassmap.domain', 'harassmap.domain.domain');
    }

    protected $domain_id = 'id';

    public function update($recordId, $context = null)
    {
        $user = BackendAuth::getUser();

        // if the user is a super use then stop here
        if (!$user->isSuperUser()) {

            $domain = DomainModel::find($recordId);
            $id = $domain->id;
            $domains = $user->domains;
            $found = false;

            foreach ($domains as $domain) {
                if($domain->id === $id) {
                    $found = true;
                    break;
                }
            }

            if(!$found) {
                throw new AccessDeniedHttpException();
            }
        }

        return $this->asExtension('FormController')->update($recordId, $context);
    }
}