<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Category;
use Harassmap\Incidents\Models\Domain;

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
        $this->page['chartReports'] = $this->getChartReports();
    }

    protected function getChartReports()
    {
        $domain = Domain::getBestMatchingDomain();
        $results = [];

        $categories = Category
            ::where('domain_id', '=', $domain->id)->get();

        foreach ($categories as $category) {
            array_push($results, [
                'title' => $category->title,
                'count' => $category->incidents()->count()
            ]);
        }

        return $results;
    }

}
