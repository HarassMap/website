<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Domain;

class ReportMap extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Report Map',
            'description' => 'Shows a map with the reports'
        ];
    }

    public function onRender()
    {
        $domain = Domain::getBestMatchingDomain();
    }

}
