<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Domain as DomainModel;
use Harassmap\Incidents\Models\Settings;

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
        $this->page['domain'] = DomainModel::getBestMatchingDomain();
        $this->page['ga_key'] = Settings::get('ga_key');
    }

}
