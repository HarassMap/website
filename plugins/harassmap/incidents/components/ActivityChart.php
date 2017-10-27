<?php

namespace Harassmap\Incidents\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use DB;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;

class ActivityChart extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Activity Chart',
            'description' => 'Shows report activity over the last 30 days.'
        ];
    }

    public function onRender()
    {
        $this->page['activityChartReports'] = $this->getChartReports();
    }

    protected function getChartReports()
    {
        $domain = Domain::getBestMatchingDomain();

        $monthAgo = new Carbon();
        $monthAgo->subMonth();
        $monthAgo->setTime(0, 0, 0);

        return Incident
            ::select([
                DB::raw('count(id) as `count`'),
                DB::raw('DATE(date) as `day`')
            ])
            ->where('domain_id', '=', $domain->id)
            ->where('is_hidden', '=', false)
            ->where('date', '>', $monthAgo)
            ->groupBy('day')
            ->get();
    }

}
