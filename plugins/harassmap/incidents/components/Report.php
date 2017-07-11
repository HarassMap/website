<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Incident;
use Redirect;

class Report extends ComponentBase
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
        $this->page['report'] = Incident::wherePublicId($id)->first();
    }

    public function onExpressSupport()
    {
        $id = $this->param('id');

        $incident = Incident::wherePublicId($id)->first();

        $incident->support++;

        $incident->save();

        return Redirect::to($this->pageUrl('reports/support', ['id' => $id]));
    }

}
