<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;

class ReportMap extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Report Map',
            'description' => 'Shows a map with the reports'
        ];
    }

    public function onGetReports()
    {
        $data = post();

        // default reports [empty]
        $reports = [];

        // if the bounds exist
        if(array_key_exists('bounds', $data)) {
            $reports = Incident::whereInsideBounds($data['bounds']);
        }

        return $reports;
    }

}
