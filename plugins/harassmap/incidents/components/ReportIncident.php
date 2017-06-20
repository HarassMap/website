<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;

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

    }

    public function onSubmit()
    {
        if (true) ;
    }

}
