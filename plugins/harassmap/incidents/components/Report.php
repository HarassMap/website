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
            'name' => 'Report Component',
            'description' => 'Exposes the report to the page.'
        ];
    }

    public function onRun()
    {
        $id = $this->param('id');

        // find the incident with the public id
        $report = Incident
            ::wherePublicId($id)
            ->where('is_hidden', '=', false)
            ->first();

        if (!$report) {
            App::abort(404);
        }

        $this->page['report'] = $report;
    }

}
