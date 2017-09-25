<?php

namespace Harassmap\Incidents\Traits;

use BackendAuth;
use Harassmap\Incidents\Models\Domain;

trait FilterDomain
{

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

        $query->whereIn($this->domain_id, $domain_ids);
    }

    public function reorderExtendQuery($query)
    {
        $this->listExtendQuery($query);
    }

    public function update($recordId, $context = null)
    {
        $user = $this->user;

        // if the user has permission then stop here
        if (!$this->hasPermission()) {

            $domain = $this->findDomain($recordId);
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

    protected function hasPermission()
    {
        return ($this->user->isSuperUser() || $this->user->hasPermission(['harassmap.incidents.domain.manage_domains']));
    }

}