<?php

namespace Harassmap\Incidents\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use DB;
use Harassmap\Incidents\Models\Category;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;

class ChartCommonReports extends ComponentBase
{

    public static $results = [];

    public function componentDetails()
    {
        return [
            'name' => 'Chart Common Reports',
        ];
    }

    public function onRender()
    {
        $this->page['circleReports'] = $this->getCircleReports();
        $this->page['lineReports'] = $this->getLineChartReports();
    }

    protected function getCircleReports()
    {
        // simple static cache
        if (!empty(self::$results)) {
            return self::$results;
        }

        $domain = Domain::getBestMatchingDomain();

        $categories = Category
            ::where('domain_id', '=', $domain->id)
            ->get();

        foreach ($categories as $category) {
            array_push(self::$results, [
                'id' => $category->id,
                'title' => $category->title,
                'count' => $category->incidents()->count()
            ]);
        }

        return self::$results;
    }

    protected function getLineChartReports()
    {
        // this gets the top 5 categories
        $sorted = array_slice(array_reverse(array_values(array_sort($this->getCircleReports(), function ($value) {
            return $value['count'];
        }))), 0, 5);

        $results = [];

        $yearAgo = new Carbon();
        $yearAgo->subYear()->subYear();
        $yearAgo->startOfMonth();

        foreach ($sorted as $item) {
            $incidents = Incident
                ::select([
                    DB::raw('count(id) as `count`'),
                    DB::raw("YEAR(date) as `year`"),
                    DB::raw("MONTH(date) as `month`"),
                ])
                ->whereHas('categories', function ($query) use ($item) {
                    $query->where('id', '=', $item['id']);
                })
                ->where('date', '>', $yearAgo)
                ->groupBy(['year', 'month'])
                ->get()
                ->map(function($item) {
                    return [
                        'count' => $item->count,
                        'year' => $item->year,
                        'month' => $item->month,
                    ] ;
                });

            array_push($results, [
                'title' => $item['title'],
                'items' => $incidents
            ]);
        }

        return $results;
    }

}
