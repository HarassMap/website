<?php

namespace Harassmap\Incidents\Components;

use App;
use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Incident;

class Report extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'View Report',
            'description' => 'Shows a single report'
        ];
    }

    public function onRun()
    {
        $id = $this->param('id');

        // find the incident with the public id
        $report = Incident::wherePublicId($id)->first();

        if (!$report) {
            App::abort(404);
        }

        $this->page['report'] = $report;
    }

}