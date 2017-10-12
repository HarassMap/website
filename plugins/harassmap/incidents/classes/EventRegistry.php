<?php

namespace Harassmap\Incidents\Classes;

use BackendAuth;
use October\Rain\Support\Traits\Singleton;
use RainLab\Pages\Classes\Page;

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

        return $this->removeDomainPages($templates, $domain_ids);
    }

    /**
     * @param $templates
     * @param $domain_ids
     * @return array
     */
    public function removeDomainPages($templates, $domain_ids) {
        $iterator = function ($pages) use (&$iterator, $domain_ids) {
            $result = [];

            foreach ($pages as $page) {

                $subPages = [];

                // get the page
                if (!($page instanceof Page)) {
                    $subPages = $page->subpages;
                    $page = $page->page;
                }

                $domain = $page->getViewBag()->property('domain');

                if (array_search($domain, $domain_ids) !== FALSE) {
                    $result[] = (object)[
                        'page' => $page,
                        'subpages' => $iterator($subPages)
                    ];
                }
            }

            return $result;
        };

        return $iterator($templates);
    }
}