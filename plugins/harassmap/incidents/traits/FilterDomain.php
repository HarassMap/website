<?php

namespace Harassmap\Incidents\Traits;

use BackendAuth;

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

        // if the user has no domains then show nothing
        if ($domains->isEmpty()) {
            $query->limit(0);
        }

        foreach ($domains as $domain) {
            $query->orWhere($this->domain_id, '=', $domain->id);
        }

    }

}