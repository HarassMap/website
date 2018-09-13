<?php

namespace Harassmap\Incidents\Components;

use App;
use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Domain;
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

    public function onRun()
    {
        $id = $this->param('id');
        $domain = Domain::getBestMatchingDomain();

        // get map pin options to the javascript side
        echo '<script>';
        echo 'var mapPins = ' . json_encode($domain->map_pin_color) . ';';
        echo '</script>';

        // find the incident with the public id
        $report = Incident
            ::where('public_id', '=', $id)
            ->where('domain_id', '=', $domain->id)
            ->where('is_hidden', '=', false)
            ->first();

        if (!$report) {
            return $this->controller->run('404');
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
