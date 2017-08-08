<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Incident;

class ReportStory extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'View Report',
            'description' => 'Shows a single report'
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
        $incident = Incident::whereApproved(true)
            ->orderBy('created_at', 'desc')
            ->first();

        $this->page['report'] = $incident;
        $this->page['viewPage'] = $this->property('viewPage');
    }

}
