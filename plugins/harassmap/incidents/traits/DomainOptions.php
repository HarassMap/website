<?php

namespace Harassmap\Incidents\Traits;

use BackendAuth;
use Harassmap\Incidents\Models\Domain;

trait DomainOptions
{

    public function getDomainIdOptions()
    {
        $user = BackendAuth::getUser();

        $choices = [];

        // if the user is a super use then return all the domains
        if ($user->isSuperUser() || $user->hasPermission(['harassmap.incidents.domain.manage_domains'])) {
            $domains = Domain::get();
        } else {
            $domains = $user->domains;
        }

        foreach ($domains as $domain) {
            $choices[$domain->id] = $domain->host;
        }

        return $choices;
    }

}