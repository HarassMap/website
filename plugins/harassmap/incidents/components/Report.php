<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Incident;
use RainLab\User\Facades\Auth;

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

}
