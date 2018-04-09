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
                'title' => 'View Page',
                'type' => 'dropdown',
                'group' => 'Links',
            ],
            'filter' => [
                'title' => 'Show Filter?',
                'type' => 'checkbox',
                'default' => true,
            ],
            'pagination' => [
                'title' => 'Show Pagination?',
                'type' => 'checkbox',
                'default' => true,
            ],
            'perPage' => [
                'title' => 'Results Per Page',
                'type' => 'string',
                'default' => '10',
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
        $perPage = (int)$this->property('perPage');

        // get the users incidents
        $this->page['reports'] = Incident
            ::where('domain_id', $domain->id)
            ->where('is_hidden', '=', false)
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        $this->page['viewPage'] = $this->property('viewPage');
        $this->page['filter'] = $this->property('filter');
        $this->page['pagination'] = $this->property('pagination');
    }

    public function onFilter()
    {
        $data = post();

        $reports = Incident::applyFilters(Incident::orderBy('date', 'desc'), $data);
        $reports = Incident::withFilters($reports)->toArray();

        $this->page['reports'] = $reports->paginate(10);
        $this->page['viewPage'] = $this->property('viewPage');
    }

}
