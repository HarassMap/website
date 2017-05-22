<?php namespace Harassmap\Domain\Controllers;

use Backend\Classes\Controller;
use BackendAuth;
use BackendMenu;
use Harassmap\Domain\Models\Tip as TipModel;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Tip extends Controller
{
    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Domain', 'harassmap.domain', 'harassmap.domain.tips');
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