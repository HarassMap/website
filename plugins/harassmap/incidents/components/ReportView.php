<?php

namespace Harassmap\Incidents\Components;

use App;
use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Classes\Mailer;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Notification;
use Harassmap\Incidents\Models\Support;
use Redirect;

class ReportView extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'View Report',
            'description' => 'Shows a single report'
        ];
    }

    public function onRender()
    {
        $id = $this->param('id');

        // find the incident with the public id
        $report = Incident::wherePublicId($id)->first();

        if (!$report) {
            App::abort(404);
        }

        $this->page['report'] = $report;
    }

    public function onExpressSupport()
    {
        $id = $this->param('id');

        // increase the support for the incident
        $incident = Incident::wherePublicId($id)->first();
        $incident->support++;
        $incident->save();

        // add a notification for incident support
        Notification::addIncidentSupport($incident);

        return Redirect::to($this->pageUrl('reports/support', ['id' => $id]));
    }

}
