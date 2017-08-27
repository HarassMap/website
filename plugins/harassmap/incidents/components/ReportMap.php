<?php

namespace Harassmap\Incidents\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use DB;
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

        $this->onGetChartReports();
    }

    public function onGetReports()
    {
        $data = post();

        // default reports [empty]
        $reports = [];

        // if the bounds exist
        if (array_key_exists('bounds', $data)) {

            if (!array_key_exists('filters', $data)) {
                $data['filters'] = [];
            }

            $reports = Incident::whereInsideBounds($data['bounds'], $data['filters']);
        }

        return $reports;
    }

    public function onGetChartReports()
    {
        $data = post();

        // default reports [empty]
        $reports = [
            'incidents' => [],
            'interventions' => []
        ];

        if (array_key_exists('time', $data)) {
            $domain = Domain::getBestMatchingDomain();

            $incidents = $this->getChartReports($domain, $data['time'])->doesntHave('intervention')->get();
            $interventions = $this->getChartReports($domain, $data['time'])->has('intervention')->get();

            foreach ($incidents as $incident) {
                $reports['incidents'][$incident->key] = $incident->count;
            };

            foreach ($interventions as $intervention) {
                $reports['interventions'][$intervention->key] = $intervention->count;
            };

        }

        return $reports;
    }

    protected function getChartReports(Domain $domain, $time)
    {

        $incidents = Incident
            ::select([
                DB::raw('count(id) as `count`')
            ])
            ->where('domain_id', '=', $domain->id);

        // get the date we are getting results from
        switch ($time) {
            case 'week':
                $since = Carbon::today()->subWeek();
                $select = DB::raw('HOUR(date) as key');
                break;
            case 'month':
                $since = Carbon::today()->subMonth();
                $select = DB::raw('DATE(date) as key');
                break;
            default:
                $since = Carbon::today()->subYear();
                $select = DB::raw('MONTH(date) as key');
        }

        // add where clause for the date
        return $incidents
            ->addSelect($select)
            ->groupBy('key')
            ->where('date', '>', $since);
    }

}
