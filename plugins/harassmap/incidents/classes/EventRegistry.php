<?php

namespace Harassmap\Incidents\Classes;

use BackendAuth;
use October\Rain\Support\Traits\Singleton;

class EventRegistry
{
    use Singleton;

    /**
     * @param array $templates
     * @return array
     */
    public function pruneDomainPages($templates)
    {
        $user = BackendAuth::getUser();

        // if the user is a super use then stop here
        if ($user->isSuperUser() || $user->hasPermission(['harassmap.incidents.domain.manage_domains'])) {
            return $templates;
        }

        $domains = $user->domains;

        if ($domains->isEmpty()) {
            return [];
        }

        $domain_ids = [];

        foreach ($domains as $domain) {
            $domain_ids[] = $domain->id;
        }

        $iterator = function ($pages) use (&$iterator, $domain_ids) {
            $result = [];

            foreach ($pages as $page) {
                $domain = $page->page->getViewBag()->property('domain');

                if (array_search($domain, $domain_ids) !== FALSE) {
                    $result[] = (object)[
                        'page' => $page->page,
                        'subpages' => $iterator($page->subpages)
                    ];
                }
            }

            return $result;
        };

        return $iterator($templates);
    }
}