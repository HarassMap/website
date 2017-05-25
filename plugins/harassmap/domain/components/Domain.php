<?php

namespace Harassmap\Domain\Components;

use Cms\Classes\ComponentBase;
use Harassmap\Domain\Models\Domain as DomainModel;

class Domain extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Domain',
            'description' => 'Makes the current domain available to the page'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        // get the list of matching domains
        $domains = DomainModel::getMatchingDomains();

        if (!empty($domains)) {
            foreach ($domains as $domain) {
                $logo = $domain->logo;

                if (empty($this->page['domain_logo']) && $logo) {
                    $this->page['domain_logo'] = $logo->getPath();
                }

                if (empty($this->page['domain_about']) && !empty($domain->about)) {
                    $this->page['domain_about'] = $domain->about;
                }

            }
        }

    }

}
