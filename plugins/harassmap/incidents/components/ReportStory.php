<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Domain;

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
        $domain = Domain::getBestMatchingDomain();

        $incident = Incident
            ::whereApproved(true)
            ->where('domain_id', '=', $domain->id)
            ->where('is_hidden', '=', false)
            ->orderBy('created_at', 'desc')
            ->first();

        $this->page['report'] = $incident;
        $this->page['viewPage'] = $this->property('viewPage');
    }

}
