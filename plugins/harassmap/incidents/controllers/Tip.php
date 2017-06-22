<?php

namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
use BackendAuth;
use BackendMenu;
use Harassmap\Incidents\Models\Tip as TipModel;
use Harassmap\Incidents\Traits\FilterDomain;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Tip extends Controller
{
    use FilterDomain;

    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('harassmap.incidents.domain', 'harassmap.incidents.domain', 'harassmap.incidents.domain.tips');
    }

    protected $domain_id = 'domain_id';

    public function update($recordId, $context = null)
    {
        $user = BackendAuth::getUser();

        // if the user is a super use then stop here
        if (!$user->isSuperUser()) {

            $content = TipModel::find($recordId);
            $id = $content->domain->id;
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
}