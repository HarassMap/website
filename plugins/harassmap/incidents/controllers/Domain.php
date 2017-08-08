<?php

namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
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
        BackendMenu::setContext('Harassmap.Incidents', 'harassmap.incidents.domain', 'harassmap.incidents.domain.domain');
    }

    protected $domain_id = 'id';

    public function update($recordId, $context = null)
    {
        $user = $this->user;

        // if the user has permission then stop here
        if (!$this->hasPermission()) {

            $domain = DomainModel::find($recordId);
            $id = $domain->id;
            $domains = $user->domains;
            $found = false;

            foreach ($domains as $domain) {
                if ($domain->id === $id) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new AccessDeniedHttpException();
            }
        }

        return $this->asExtension('FormController')->update($recordId, $context);
    }

    public function create($context = null)
    {
        // if the user has permission then stop here
        if (!$this->hasPermission()) {
            throw new AccessDeniedHttpException();
        }

        return $this->asExtension('FormController')->create($context);
    }

    public function hasPermission()
    {
        return ($this->user->isSuperUser() || $this->user->hasPermission(['harassmap.incidents.domain.manage_domains']));
    }
}