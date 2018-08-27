<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use DB;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Settings;
use October\Rain\Support\Collection as Collection;
use Log;

class ReportChart extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Report Chart',
            'description' => 'Shows a chart of reports over time'
        ];
    }

    public function onRender()
    {
        $pinColor = Settings::get('map_pins');
        $domain = Domain::getBestMatchingDomain();
        $setDomain = Settings::set('domain_name', $domain->host);

        $collection = new Collection([$setDomain => $pinColor]);

        $domain->map_pin_color = $pinColor;

        Log::info($collection);

        // get map pin options to the javascript side
        echo '<script>';
        echo 'var mapPins = ' . json_encode($domain->map_pin_color) . ';';
        echo '</script>';

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

        $reports['domain_host'] = $domain->host;

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
            ->where('is_hidden', '=', false)
            ->groupBy('day');
    }

}
