<?php

namespace Harassmap\Incidents\Components;

use App;
use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Classes\Mailer;
use Harassmap\Incidents\Models\Incident;

class ExpressSupport extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Express Support Page',
            'description' => ''
        ];
    }

    public function onRender()
    {
        $id = $this->param('id');

        // find the incident with the public id
        $report = Incident
            ::wherePublicId($id)
            ->where('is_hidden', '=', false)
            ->first();

        if (!$report) {
            return $this->controller->run('404');
        }

        $this->page['report'] = $report;
    }

}
