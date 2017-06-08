<?php

namespace Harassmap\Incidents\Components;

use ApplicationException;
use Cms\Classes\ComponentBase;
use Harassmap\Domain\Models\Content;
use Harassmap\Domain\Models\Domain;

class ReportIncident extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Report Incident',
            'description' => 'Render a report incident form.'
        ];
    }

    public function onSubmit()
    {
        if(true);
    }

}
