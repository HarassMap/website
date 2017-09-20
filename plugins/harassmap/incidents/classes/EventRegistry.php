<?php

namespace Harassmap\Incidents\Classes;

use BackendAuth;
use Cms\Classes\CmsObjectCollection;
use October\Rain\Support\Traits\Singleton;

class EventRegistry
{
    use Singleton;

    /**
     * @param CmsObjectCollection $templates
     * @return CmsObjectCollection
     */
    public function pruneDomainContentTemplates($templates)
    {
        $user = BackendAuth::getUser();

        // if the user is a super use then stop here
        if ($user->isSuperUser() || $user->hasPermission(['harassmap.incidents.domain.manage_domains'])) {
            return $templates;
        }

        $domains = $user->domains;

        if ($domains->isEmpty()) {
            return $templates->filter(function () {
                return false;
            });
        }

        return $templates->filter(function ($template) use ($domains) {


        });
    }
}