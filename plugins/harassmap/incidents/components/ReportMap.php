<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
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
        $this->page['reports'] = Incident::orderBy('date', 'desc')->limit(5)->get();

        $this->page['viewPage'] = $this->property('viewPage');
    }

    public function onGetReports()
    {
        $data = post();

        // default reports [empty]
        $reports = [];

        // if the bounds exist
        if (array_key_exists('bounds', $data)) {
            $reports = Incident::whereInsideBounds($data['bounds']);
        }

        return $reports;
    }

}
