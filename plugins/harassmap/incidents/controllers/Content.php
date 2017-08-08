<?php

namespace Harassmap\Incidents\Controllers;

use Harassmap\Incidents\Models\Content as ContentModel;
use Backend\Classes\Controller;
use BackendAuth;
use BackendMenu;
use Harassmap\Incidents\Traits\FilterDomain;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Content extends Controller
{
    use FilterDomain;

    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('harassmap.incidents.domain', 'harassmap.incidents.domain', 'harassmap.incidents.domain.content');
    }

    protected $domain_id = 'domain_id';

    public function update($recordId, $context = null)
    {
        $user = BackendAuth::getUser();

        // if the user is a super use then stop here
        if (!($user->isSuperUser() || $user->hasPermission(['harassmap.incidents.domain.manage_domains']))) {

            $content = ContentModel::find($recordId);
            $id = $content->domain->id;
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