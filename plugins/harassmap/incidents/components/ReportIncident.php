<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Harassmap\Domain\Models\Domain;
use Harassmap\Incidents\Models\Country;

class ReportIncident extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Report Incident',
            'description' => 'Render a report incident form.'
        ];
    }

    public function onRender()
    {
        $this->page['countries'] = Country::all()->lists('name', 'id');
        $this->page['domain'] = Domain::getBestMatchingDomain();
    }

    public function onSubmit()
    {
        if (true) ;
    }

}
