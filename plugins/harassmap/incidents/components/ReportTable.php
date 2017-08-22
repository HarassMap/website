<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;

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
        $domain = Domain::getBestMatchingDomain();

        // get the users incidents
        $this->page['reports'] = Incident
            ::where('domain_id', $domain->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        $this->page['viewPage'] = $this->property('viewPage');
    }

    public function onFilter()
    {
        $data = post();

        $reports = Incident::applyFilters(Incident::orderBy('date', 'desc'), $data);

        $this->page['reports'] = $reports->paginate(10);
        $this->page['viewPage'] = $this->property('viewPage');
    }

}
