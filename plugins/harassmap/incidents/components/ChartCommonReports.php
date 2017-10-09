<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use DB;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;

class ChartCommonReports extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Chart Common Reports',
        ];
    }

    public function onRender()
    {
        $this->page['chartReports'] = $this->onGetChartReports();
    }

    public function onGetChartReports()
    {
        // default reports [empty]
        $reports = [
            'incident' => [],
            'intervention' => []
        ];

        $domain = Domain::getBestMatchingDomain();

        $incidents = $this->getChartReports($domain)->doesntHave('intervention')->get();
        $interventions = $this->getChartReports($domain)->has('intervention')->get();

        foreach ($incidents as $incident) {
            $reports['incident'][$incident->day] = $incident->count;
        };

        foreach ($interventions as $intervention) {
            $reports['intervention'][$intervention->day] = $intervention->count;
        };

        return $reports;
    }

    protected function getChartReports(Domain $domain)
    {
        return Incident
            ::select([
                DB::raw('count(id) as `count`'),
                DB::raw('DATE(date) as `day`')
            ])
            ->where('domain_id', '=', $domain->id)
            ->groupBy('day');
    }

}
