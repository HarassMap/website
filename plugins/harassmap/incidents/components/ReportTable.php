<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Incident;
use RainLab\User\Facades\Auth;

class ReportTable extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Report Table',
            'description' => 'Shows a table of all the reports'
        ];
    }

    public function defineProperties()
    {
        return [
            'viewPage' => [
                'title' => 'harassmap.incidents::lang.tip.list.page_name',
                'description' => 'harassmap.incidents::lang.tip.list.page_help',
                'type' => 'dropdown',
                'group' => 'Links',
            ],
        ];
    }

    public function getViewPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRender()
    {
        // get the users incidents
        $this->page['reports'] = Incident::orderBy('created_at', 'desc')->paginate(10);
        $this->page['viewPage'] = $this->property('viewPage');
    }

}
