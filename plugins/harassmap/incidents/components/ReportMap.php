<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Domain;
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
                'title' => 'Report View Page',
                'type' => 'dropdown',
                'group' => 'Links',
            ],
            'tablePage' => [
                'title' => 'Table View Page',
                'type' => 'dropdown',
                'group' => 'Links',
            ],
        ];
    }

    public function getViewPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getTablePageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRender()
    {
        $domain = Domain::getBestMatchingDomain();

        $this->page['reports'] = Incident
            ::where('domain_id', $domain->id)
            ->orderBy('date', 'desc')
            ->limit(4)
            ->get();

        $this->page['viewPage'] = $this->property('viewPage');
        $this->page['tablePage'] = $this->property('tablePage');
    }

    public function onGetReports()
    {
        $data = post();

        if (!array_key_exists('filters', $data)) {
            $data['filters'] = [];
        }

        $reports = Incident::withFilters($data['filters']);

        return $reports;
    }

}
